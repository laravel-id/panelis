<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\MailType;
use App\Mail\TestMail;
use App\Models\Branch;
use App\Models\Setting;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as Mailer;

class Mail extends Page implements HasForms, Settings\HasUpdateableForm
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 3;

    public array $mail;

    public array $services;

    public bool $isButtonDisabled;

    private function senderSection(): Section
    {
        return Section::make(__('setting.mail_sender'))
            ->description(__('setting.mail_sender_section_description'))
            ->collapsed()
            ->schema([
                TextInput::make('mail.from.address')
                    ->label(__('setting.mail_from_address'))
                    ->email()
                    ->required(),

                TextInput::make('mail.from.name')
                    ->label(__('setting.mail_from_name'))
                    ->string()
                    ->required(),
            ]);
    }

    private function driverSection(): Section
    {
        return

            Section::make(__('setting.mail'))
                ->description(__('setting.mail_section_description'))
                ->schema([
                    Radio::make('mail.default')
                        ->label(__('setting.mail_driver'))
                        ->options(MailType::options())
                        ->descriptions(MailType::descriptions())
                        ->live()
                        ->required(),
                ]);
    }

    private function sendmailSection(): Section
    {
        return

            Section::make(__('setting.mail_sendmail'))
                ->visible(fn (Get $get): bool => $get('mail.default') === MailType::Sendmail->value)
                ->schema([
                    TextInput::make('mail.mailers.sendmail.path')
                        ->label(__('setting.mail_sendmail_path'))
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

        return Section::make(__('setting.mail_smtp'))
            ->visible(fn (Get $get): bool => $get('mail.default') === MailType::SMTP->value)
            ->schema([
                TextInput::make('mail.mailers.smtp.host')
                    ->label(__('setting.mail_smtp_host'))
                    ->password($isDemo)
                    ->helperText($demoText)
                    ->required(),

                TextInput::make('mail.mailers.smtp.port')
                    ->label(__('setting.mail_smtp_port'))
                    ->integer()
                    ->required(),

                TextInput::make('mail.mailers.smtp.username')
                    ->label(__('setting.mail_smtp_username'))
                    ->password($isDemo)
                    ->helperText($demoText)
                    ->autocomplete(false)
                    ->nullable(),

                TextInput::make('mail.mailers.smtp.password')
                    ->label(__('setting.mail_smtp_password'))
                    ->autocomplete(false)
                    ->password()
                    ->revealable()
                    ->nullable(),

                Radio::make('mail.mailers.smtp.encryption')
                    ->label(__('setting.mail_smtp_encryption'))
//                         ->required()
                    ->options([
                        '' => __('setting.mail_encryption_none'),
                        'ssl' => 'SSL',
                        'tls' => 'TLS',
                        'starttls' => 'STARTTLS',
                    ]),
            ]);
    }

    private function mailgunSection(): Section
    {
        return

            Section::make(__('setting.mail_mailgun'))
                ->visible(fn (Get $get): bool => $get('mail.default') === MailType::Mailgun->value)
                ->schema([
                    TextInput::make('services.mailgun.domain')
                        ->label(__('setting.mail_mailgun_domain'))
                        ->string()
                        ->required(),

                    TextInput::make('services.mailgun.secret')
                        ->label(__('setting.mail_mailgun_secret'))
                        ->password()
                        ->revealable()
                        ->required(),

                    TextInput::make('services.mailgun.endpoint')
                        ->label(__('setting.mail_mailgun_endpoint'))
                        ->string()
                        ->required(),
                ]);
    }

    private function postmarkSection(): Section
    {
        return

            Section::make(__('setting.mail_postmark'))
                ->visible(fn (Get $get): bool => $get('mail.default') === MailType::Postmark->value)
                ->schema([
                    TextInput::make('services.postmark.token')
                        ->label(__('setting.mail_postmark_token'))
                        ->password()
                        ->revealable()
                        ->required(),
                ]);
    }

    private function sesSection(): Section
    {
        return

            Section::make(__('setting.mail_ses'))
                ->visible(fn (Get $get): bool => $get('mail.default') === MailType::SES->value)
                ->schema([
                    TextInput::make('services.ses.key')
                        ->label(__('setting.mail_ses_key'))
                        ->required(),

                    TextInput::make('services.ses.secret')
                        ->label(__('setting.mail_ses_secret'))
                        ->password()
                        ->revealable()
                        ->required(),

                    TextInput::make('services.ses.region')
                        ->label(__('setting.mail_ses_region'))
                        ->required(),
                ]);
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting.mail');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting_mail');
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('test_mail')
                ->label(__('setting.mail_button_test'))
                ->modalWidth(MaxWidth::Medium)
                ->modalSubmitActionLabel(__('setting.mail_test_button_send'))
                ->form([
                    Radio::make('send_from')
                        ->label(__('setting.mail_send_from'))
                        ->default('mail')
                        ->live()
                        ->required()
                        ->options([
                            'mail' => __('setting.mail_app_email'),
                            'branch' => __('setting.mail_branch_email'),
                        ]),

                    TextInput::make('from')
                        ->label(__('setting.mail_from_address'))
                        ->default(config('mail.from.address'))
                        ->readOnly()
                        ->visible(fn (Get $get): bool => $get('send_from') === 'mail')
                        ->required(),

                    Select::make('branch')
                        ->label(__('setting.mail_from_address'))
                        ->searchable()
                        ->visible(fn (Get $get): bool => $get('send_from') === 'branch')
                        ->helperText(__('setting.mail_branch_empty_help'))
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
                        ->label(__('setting.mail_to_address'))
                        ->helperText(__('setting.mail_email_helper'))
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

                        Notification::make('test_mail_success')
                            ->success()
                            ->title(__('setting.mail_test_success'))
                            ->body(__('setting.mail_test_instruction'))
                            ->send();
                    } catch (Exception $e) {
                        Log::error($e);

                        Notification::make('test_mail_failed')
                            ->danger()
                            ->color('danger')
                            ->title(__('setting.mail_test_failed'))
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

            'isButtonDisabled' => config('app.demo'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            $this->senderSection(),
            $this->driverSection(),
            $this->sendmailSection(),
            $this->smtpSection(),
            $this->mailgunSection(),
            $this->postmarkSection(),
            $this->sesSection(),
        ])->disabled(config('app.demo'));
    }

    public function update(): void
    {
        $this->validate();

        if (config('app.demo')) {
            return;
        }

        foreach (Arr::dot($this->form->getState()) as $key => $value) {
            if (empty($value)) {
                $value = '';
            }
            Setting::updateOrCreate(compact('key'), compact('value'));
        }

        event(new SettingUpdated);

        Notification::make()
            ->success()
            ->title(__('setting.mail_updated'))
            ->send();
    }
}
