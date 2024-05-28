<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Mail\TestMail;
use App\Models\Setting;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail as Mailer;

class Mail extends Page
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-at-symbol';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    public array $mail;

    public function getTitle(): string|Htmlable
    {
        return __('setting.mail');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.mail');
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('test_mail')
                ->label(__('setting.mail_test_button'))
                ->requiresConfirmation()
                ->modalSubmitActionLabel(__('setting.mail_test_button_send'))
                ->form([
                    TextInput::make('email')
                        ->label(__('setting.email'))
                        ->helperText(__('setting.mail_email_helper'))
                        ->email()
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        Mailer::to($data['email'])->send(new TestMail);

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
            'mail.mailers.smtp' => config('mail.mailers.smtp'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('setting.mail'))
                ->description(__('setting.mail_info'))
                ->schema([
                    TextInput::make('mail.mailers.smtp.host')
                        ->label('setting.mail_host')
                        ->required(),

                    TextInput::make('mail.mailers.smtp.port')
                        ->label('setting.mail_port')
                        ->integer()
                        ->required(),

                    TextInput::make('mail.mailers.smtp.username')
                        ->label('setting.mail_username')
                        ->autocomplete(false)
                        ->nullable(),

                    TextInput::make('mail.mailers.smtp.password')
                        ->label('setting.mail_password')
                        ->autocomplete(false)
                        ->password()
                        ->revealable()
                        ->nullable(),

                    Radio::make('mail.mailers.smtp.encryption')
                        ->label(__('setting.mail_encryption'))
                        // ->required()
                        ->options([
                            '' => __('setting.mail_encryption_none'),
                            'ssl' => 'SSL',
                            'tls' => 'TLS',
                            'starttls' => 'STARTTLS',
                        ]),
                ]),
        ]);
    }

    public function update(): void
    {
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
            ->title(__('setting.mail_updated'))
            ->send();
    }
}
