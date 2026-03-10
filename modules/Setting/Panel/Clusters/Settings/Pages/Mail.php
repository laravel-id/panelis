<?php

namespace Modules\Setting\Panel\Clusters\Settings\Pages;

use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as Mailer;
use Illuminate\Validation\ValidationException;
use Modules\Branch\Models\Branch;
use Modules\Setting\Events\SettingUpdated;
use Modules\Setting\Mail\TestMail;
use Modules\Setting\Models\Setting;
use Modules\Setting\Panel\Clusters\Settings;
use Modules\Setting\Panel\Clusters\Settings\Enums\MailPermission;
use Modules\Setting\Panel\Clusters\Settings\Enums\MailType;
use Modules\Setting\Panel\Clusters\Settings\HasUpdateableForm;
use Modules\Setting\Panel\Clusters\Settings\UpdateSettingPage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Mail extends UpdateSettingPage implements HasSchemas, HasUpdateableForm
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAtSymbol;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 30;

    public array $mail;

    public array $services;

    public string $version = '';

    private function senderSection(): Section
    {
        return Section::make(__('setting::setting.mail.sender'))
            ->description(__('setting::setting.mail.sender_section_description'))
            ->collapsed()
            ->schema([
                TextInput::make('mail.from.address')
                    ->label(__('setting::setting.mail.from_address'))
                    ->email()
                    ->required(),

                TextInput::make('mail.from.name')
                    ->label(__('setting::setting.mail.from_name'))
                    ->string()
                    ->required(),
            ]);
    }

    private function driverSection(): Section
    {
        return Section::make(__('setting::setting.mail.label'))
            ->description(__('setting::setting.mail.section_description'))
            ->schema([
                Radio::make('mail.default')
                    ->label(__('setting::setting.mail.driver'))
                    ->options(MailType::class)
                    ->enum(MailType::class)
                    ->live()
                    ->required(),
            ]);
    }

    private function sendmailSection(): Section
    {
        return Section::make(__('setting::setting.mail.sendmail.driver'))
            ->visible(fn (Get $get): bool => $get('mail.default') === MailType::Sendmail)
            ->schema([
                TextInput::make('mail.mailers.sendmail.path')
                    ->label(__('setting::setting.mail.sendmail.path'))
                    ->required(),
            ]);
    }

    private function smtpSection(): Section
    {
        $isDemo = config('panelis.demo');
        $demoText = function (): ?string {
            if (config('panelis.demo')) {
                return __('setting::setting.hidden_when_in_demo');
            }

            return null;
        };

        return Section::make(__('setting::setting.mail.smtp.driver'))
            ->visible(fn (Get $get): bool => $get('mail.default') === MailType::SMTP)
            ->schema([
                TextInput::make('mail.mailers.smtp.host')
                    ->label(__('setting::setting.mail.smtp.host'))
                    ->password($isDemo)
                    ->helperText($demoText)
                    ->required(),

                TextInput::make('mail.mailers.smtp.port')
                    ->label(__('setting::setting.mail.smtp.port'))
                    ->integer()
                    ->required(),

                TextInput::make('mail.mailers.smtp.username')
                    ->label(__('setting::setting.mail.smtp.username'))
                    ->password($isDemo)
                    ->helperText($demoText)
                    ->autocomplete(false)
                    ->nullable(),

                TextInput::make('mail.mailers.smtp.password')
                    ->label(__('setting::setting.mail.smtp.password'))
                    ->autocomplete(false)
                    ->password()
                    ->revealable()
                    ->nullable(),

                Radio::make('mail.mailers.smtp.encryption')
                    ->label(__('setting::setting.mail.smtp.encryption'))
                    ->options([
                        '' => __('setting::setting.mail.smtp.encryption_none'),
                        'ssl' => 'SSL',
                        'tls' => 'TLS',
                        'starttls' => 'STARTTLS',
                    ]),
            ]);
    }

    private function mailgunSection(): Section
    {
        return Section::make(__('setting::setting.mail.mailgun.driver'))
            ->visible(fn (Get $get): bool => $get('mail.default') === MailType::Mailgun)
            ->disabled(! MailType::Mailgun->installed())
            ->schema([
                Callout::make(__('setting::setting.mail.mailgun.no_package_title'))
                    ->description(__('setting::setting.mail.mailgun.no_package_description'))
                    ->visible(! MailType::Mailgun->installed())
                    ->warning()
                    ->actions([
                        Action::make('view_doc')
                            ->label(__('setting::setting.mail.btn.view_doc'))
                            ->url(sprintf('https://laravel.com/docs/%s.x/mail#mailgun-driver', $this->version)),
                    ]),

                TextInput::make('services.mailgun.domain')
                    ->label(__('setting::setting.mail.mailgun.domain'))
                    ->string()
                    ->required(),

                TextInput::make('services.mailgun.secret')
                    ->label(__('setting::setting.mail.mailgun.secret'))
                    ->password()
                    ->revealable()
                    ->required(),

                TextInput::make('services.mailgun.endpoint')
                    ->label(__('setting::setting.mail.mailgun.endpoint'))
                    ->string()
                    ->required(),
            ]);
    }

    private function postmarkSection(): Section
    {
        return Section::make(__('setting::setting.mail.postmark.driver'))
            ->visible(fn (Get $get): bool => $get('mail.default') === MailType::Postmark)
            ->disabled(! MailType::Postmark->installed())
            ->schema([
                Callout::make(__('setting::setting.mail.postmark.no_package_title'))
                    ->description(__('setting::setting.mail.postmark.no_package_description'))
                    ->visible(! MailType::Postmark->installed())
                    ->warning()
                    ->actions([
                        Action::make('view_doc')
                            ->label(__('setting::setting.mail.btn.view_doc'))
                            ->url(sprintf('https://laravel.com/docs/%s.x/mail#postmark-driver', $this->version)),
                    ]),

                TextInput::make('services.postmark.key')
                    ->label(__('setting::setting.mail.postmark.key'))
                    ->password()
                    ->revealable()
                    ->required(),
            ]);
    }

    private function resendSection(): Section
    {
        return Section::make(__('setting::setting.mail.resend.driver'))
            ->visible(fn (Get $get): bool => $get('mail.default') === MailType::Resend)
            ->disabled(! MailType::Resend->installed())
            ->schema([
                Callout::make(__('setting::setting.mail.resend.no_package_title'))
                    ->description(__('setting::setting.mail.resend.no_package_description'))
                    ->visible(! MailType::Resend->installed())
                    ->warning()
                    ->actions([
                        Action::make('view_doc')
                            ->label(__('setting::setting.mail.btn.view_doc'))
                            ->url(sprintf('https://laravel.com/docs/%s.x/mail#resend-driver', $this->version)),
                    ]),

                TextInput::make('services.resend.key')
                    ->label(__('setting::setting.mail.resend.key'))
                    ->password()
                    ->revealable()
                    ->required(),
            ]);
    }

    private function sesSection(): Section
    {
        return Section::make(__('setting::setting.mail.ses.driver'))
            ->visible(fn (Get $get): bool => $get('mail.default') === MailType::SES)
            ->disabled(! MailType::SES->installed())
            ->schema([
                Callout::make(__('setting::setting.mail.ses.no_package_title'))
                    ->description(__('setting::setting.mail.ses.no_package_description'))
                    ->visible(! MailType::SES->installed())
                    ->warning()
                    ->actions([
                        Action::make('veiw_doc')
                            ->label(__('setting::setting.mail.btn.view_doc'))
                            ->url(sprintf('https://laravel.com/docs/%s.x/mail#ses-driver', $this->version)),
                    ]),

                TextInput::make('services.ses.key')
                    ->label(__('setting::setting.mail.ses.key'))
                    ->required(),

                TextInput::make('services.ses.secret')
                    ->label(__('setting::setting.mail.ses.secret'))
                    ->password()
                    ->revealable()
                    ->required(),

                TextInput::make('services.ses.region')
                    ->label(__('setting::setting.mail.ses.region'))
                    ->required(),
            ]);
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting::setting.mail.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting::setting.mail.navigation');
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
                ->label(__('setting::setting.mail.btn.test_send'))
                ->modalWidth(Width::Medium)
                ->modalSubmitActionLabel(__('setting::setting.mail.btn.test_send'))
                ->schema([
                    Radio::make('send_from')
                        ->label(__('setting::setting.mail.send_from'))
                        ->default('mail')
                        ->live()
                        ->required()
                        ->options([
                            'mail' => __('setting::setting.mail.app_email'),
                            'branch' => __('setting::setting.mail.branch_email'),
                        ]),

                    TextInput::make('from')
                        ->label(__('setting::setting.mail.from_address'))
                        ->default(config('mail.from.address'))
                        ->readOnly()
                        ->visible(fn (Get $get): bool => $get('send_from') === 'mail')
                        ->required(),

                    Select::make('branch')
                        ->label(__('setting::setting.mail.from_address'))
                        ->searchable()
                        ->visible(fn (Get $get): bool => $get('send_from') === 'branch')
                        ->helperText(__('setting::setting.mail.branch_empty_help'))
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
                        ->label(__('setting::setting.mail.to_address'))
                        ->helperText(__('setting::setting.mail.email.helper'))
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
                            ->title(__('setting::setting.mail.test_success'))
                            ->body(__('setting::setting.mail.test_instruction'))
                            ->send();
                    } catch (Exception $e) {
                        Log::error($e);

                        Notification::make('test_mail.failed')
                            ->danger()
                            ->color('danger')
                            ->title(__('setting::setting.mail.test_failed'))
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

        $this->version = array_first(explode('.', app()->version())) ?? '12';
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
            $this->resendSection(),
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

        try {
            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                if (empty($value)) {
                    $value = '';
                }
                Setting::updateOrCreate(compact('key'), compact('value'));
            }

            event(new SettingUpdated);

            Notification::make()
                ->success()
                ->title(__('setting::setting.notifications.updated.title'))
                ->send();
        } catch (Throwable $e) {
            Log::error($e);

            Notification::make('theme_not_updated')
                ->title(__('setting::setting.notifications.update_failed.title'))
                ->body($e->getMessage())
                ->warning()
                ->send();
        }
    }
}
