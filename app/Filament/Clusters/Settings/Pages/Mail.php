<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\MailPermission;
use App\Filament\Clusters\Settings\Enums\MailType;
use App\Filament\Clusters\Settings\HasUpdateableForm;
use App\Mail\TestMail;
use App\Models\Branch;
use App\Models\Setting;
use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as Mailer;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Mail extends Page implements HasForms, HasUpdateableForm
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAtSymbol;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::AtSymbol;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 3;

    public array $mail;

    public array $services;

    private function senderSection(): Section
    {
        return Section::make(__('setting.mail.sender'))
            ->description(__('setting.mail.sender_section_description'))
            ->collapsed()
            ->schema([
                TextInput::make('mail.from.address')
                    ->label(__('setting.mail.from_address'))
                    ->email()
                    ->required(),

                TextInput::make('mail.from.name')
                    ->label(__('setting.mail.from_name'))
                    ->string()
                    ->required(),
            ]);
    }

    private function driverSection(): Section
    {
        return

            Section::make(__('setting.mail.label'))
                ->description(__('setting.mail.section_description'))
                ->schema([
                    Radio::make('mail.default')
                        ->label(__('setting.mail.driver'))
                        ->options(MailType::class)
                        ->enum(MailType::class)
                        ->live()
                        ->required(),
                ]);
    }

    private function sendmailSection(): Section
    {
        return

            Section::make(__('setting.mail.sendmail_driver'))
                ->visible(fn (Get $get): bool => $get('mail.default') === MailType::Sendmail)
                ->schema([
                    TextInput::make('mail.mailers.sendmail.path')
                        ->label(__('setting.mail.sendmail.path'))
                        ->required(),
                ]);
    }

    private function smtpSection(): Section
    {
        $isDemo = config('app.demo');
        $demoText = function (): ?string {
            if (config('app.demo')) {
                return __('setting.hidden_when_in_demo');
            }

            return null;
        };

        return Section::make(__('setting.mail.smtp_driver'))
            ->visible(fn (Get $get): bool => $get('mail.default') === MailType::SMTP)
            ->schema([
                TextInput::make('mail.mailers.smtp.host')
                    ->label(__('setting.mail.smtp_host'))
                    ->password($isDemo)
                    ->helperText($demoText)
                    ->required(),

                TextInput::make('mail.mailers.smtp.port')
                    ->label(__('setting.mail.smtp_port'))
                    ->integer()
                    ->required(),

                TextInput::make('mail.mailers.smtp.username')
                    ->label(__('setting.mail.smtp_username'))
                    ->password($isDemo)
                    ->helperText($demoText)
                    ->autocomplete(false)
                    ->nullable(),

                TextInput::make('mail.mailers.smtp.password')
                    ->label(__('setting.mail.smtp_password'))
                    ->autocomplete(false)
                    ->password()
                    ->revealable()
                    ->nullable(),

                Radio::make('mail.mailers.smtp.encryption')
                    ->label(__('setting.mail.smtp_encryption'))
                    //                         ->required()
                    ->options([
                        '' => __('setting.mail.encryption_none'),
                        'ssl' => 'SSL',
                        'tls' => 'TLS',
                        'starttls' => 'STARTTLS',
                    ]),
            ]);
    }

    private function mailgunSection(): Section
    {
        return

            Section::make(__('setting.mail.mailgun_driver'))
                ->visible(fn (Get $get): bool => $get('mail.default') === MailType::Mailgun)
                ->schema([
                    TextInput::make('services.mailgun.domain')
                        ->label(__('setting.mail.mailgun_domain'))
                        ->string()
                        ->required(),

                    TextInput::make('services.mailgun.secret')
                        ->label(__('setting.mail.mailgun_secret'))
                        ->password()
                        ->revealable()
                        ->required(),

                    TextInput::make('services.mailgun.endpoint')
                        ->label(__('setting.mail.mailgun_endpoint'))
                        ->string()
                        ->required(),
                ]);
    }

    private function postmarkSection(): Section
    {
        return

            Section::make(__('setting.mail.postmark_driver'))
                ->visible(fn (Get $get): bool => $get('mail.default') === MailType::Postmark)
                ->schema([
                    TextInput::make('services.postmark.key')
                        ->label(__('setting.mail.postmark_key'))
                        ->password()
                        ->revealable()
                        ->required(),
                ]);
    }

    private function sesSection(): Section
    {
        return

            Section::make(__('setting.mail.ses_driver'))
                ->visible(fn (Get $get): bool => $get('mail.default') === MailType::SES)
                ->schema([
                    TextInput::make('services.ses.key')
                        ->label(__('setting.mail.ses_key'))
                        ->required(),

                    TextInput::make('services.ses.secret')
                        ->label(__('setting.mail.ses_secret'))
                        ->password()
                        ->revealable()
                        ->required(),

                    TextInput::make('services.ses.region')
                        ->label(__('setting.mail.ses_region'))
                        ->required(),
                ]);
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting.mail.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.mail.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(MailPermission::Browse);
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('test_mail')
                ->visible(user_can(MailPermission::SendTest))
                ->label(__('setting.mail.btn.test_send'))
                ->modalWidth(Width::Medium)
                ->modalSubmitActionLabel(__('setting.mail.btn.test_send'))
                ->schema([
                    Radio::make('send_from')
                        ->label(__('setting.mail.send_from'))
                        ->default('mail')
                        ->live()
                        ->required()
                        ->options([
                            'mail' => __('setting.mail.app_email'),
                            'branch' => __('setting.mail.branch_email'),
                        ]),

                    TextInput::make('from')
                        ->label(__('setting.mail.from_address'))
                        ->default(config('mail.from.address'))
                        ->readOnly()
                        ->visible(fn (Get $get): bool => $get('send_from') === 'mail')
                        ->required(),

                    Select::make('branch')
                        ->label(__('setting.mail.from_address'))
                        ->searchable()
                        ->visible(fn (Get $get): bool => $get('send_from') === 'branch')
                        ->helperText(__('setting.mail.branch_empty_help'))
                        ->required()
                        ->options(
                            Branch::whereNotNull('email')
                                ->orderBy('name')
                                ->get()
                                ->mapWithKeys(fn (Branch $branch): array => [
                                    $branch->id => sprintf('%s (%s)', $branch->name, $branch->email),
                                ])
                        ),

                    TextInput::make('to')
                        ->label(__('setting.mail.to_address'))
                        ->helperText(__('setting.mail.email.helper'))
                        ->email()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        $from = [
                            'address' => config('mail.from.address'),
                            'name' => config('mail.from.name'),
                        ];

                        if (! empty($data['branch'])) {
                            $branch = Branch::find($data['branch']);
                            if (! empty($branch)) {
                                $from = [
                                    'address' => $branch->email,
                                    'name' => $branch->name,
                                ];
                            }
                        }

                        Mailer::to($data['to'])
                            ->send(new TestMail(...$from));

                        Notification::make('test_mail.success')
                            ->success()
                            ->title(__('setting.mail.test_success'))
                            ->body(__('setting.mail.test_instruction'))
                            ->send();
                    } catch (Exception $e) {
                        Log::error($e);

                        Notification::make('test_mail.failed')
                            ->danger()
                            ->color('danger')
                            ->title(__('setting.mail.test_failed'))
                            ->body($e->getMessage())
                            ->persistent()
                            ->send();
                    }
                }),
        ];
    }

    public function mount(): void
    {
        $this->form->fill([
            'mail' => [
                'default' => config('mail.default'),
                'mailers' => config('mail.mailers'),
                'from' => [
                    'address' => config('mail.from.address', config('app.email')),
                    'name' => config('mail.from.name', config('app.name')),
                ],
            ],

            'services' => config('services'),

            'isButtonDisabled' => user_cannot(MailPermission::Edit),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            $this->senderSection(),
            $this->driverSection(),
            $this->sendmailSection(),
            $this->smtpSection(),
            $this->mailgunSection(),
            $this->postmarkSection(),
            $this->sesSection(),
        ])->disabled(! user_can(MailPermission::Edit));
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(user_can(MailPermission::Edit), Response::HTTP_FORBIDDEN);

        $this->validate();

        foreach (Arr::dot($this->form->getState()) as $key => $value) {
            if (empty($value)) {
                $value = '';
            }
            Setting::updateOrCreate(compact('key'), compact('value'));
        }

        event(new SettingUpdated);

        Notification::make()
            ->success()
            ->title(__('filament-actions::edit.single.notifications.saved.title'))
            ->send();
    }
}
