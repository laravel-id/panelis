<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\LogChannel;
use App\Filament\Clusters\Settings\Enums\LogPermission;
use App\Models\Setting;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Log extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 6;

    public array $logging;

    public array $nightwatch = [];

    public bool $isButtonDisabled = false;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_log')
                ->label(__('setting.log_button_test'))
                ->modalWidth(MaxWidth::Medium)
                ->form([
                    Textarea::make('message')
                        ->label(__('setting.log_message'))
                        ->required(),
                ])
                ->action(function (array $data): void {
                    try {
                        Logger::debug($data['message'] ?? 'Testing log');

                        Notification::make('log_test_sent')
                            ->title(__('setting.log_test_sent'))
                            ->success()
                            ->send();

                    } catch (Exception) {
                    }
                }),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting.log');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting_log');
    }

    public static function canAccess(): bool
    {
        return user_can(LogPermission::Browse);
    }

    public function mount(): void
    {
        $logging = config('logging.channels.stack');

        $this->form->fill([
            'logging' => [
                'channels' => [
                    'stack' => [
                        'channels' => $logging['channels'],
                    ],

                    LogChannel::Slack->value => [
                        'username' => config('logging.channels.slack.username'),
                        'url' => config('logging.channels.slack.url'),
                        'level' => config('logging.channels.slack.level'),
                    ],

                    LogChannel::Papertrail->value => [
                        'level' => config('logging.channels.papertrail.level'),
                        'url' => config('logging.channels.papertrail.url'),
                        'port' => config('logging.channels.papertrail.port', 514),
                    ],
                ],
            ],

            LogChannel::Nightwatch->value => [
                'enabled' => config('nightwatch.enabled'),
                'token' => config('nightwatch.token'),
                'sampling' => [
                    'requests' => config('nightwatch.sampling.requests'),
                    'commands' => config('nightwatch.sampling.commands'),
                    'exceptions' => config('nightwatch.sampling.exceptions'),
                ],
            ],

            'isButtonDisabled' => user_cannot(LogPermission::Browse),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(user_cannot(LogPermission::Edit))
            ->schema([
                Section::make(__('setting.log'))
                    ->description(__('setting.log_section_description'))
                    ->schema([
                        CheckboxList::make('logging.channels.stack.channels')
                            ->label(__('setting.log_channel'))
                            ->descriptions(LogChannel::descriptions())
                            ->live()
                            ->required()
                            ->options(LogChannel::options())
                            ->disableOptionWhen(function (string $value): bool {
                                return $value === LogChannel::Nightwatch->value && ! $this->nightwatchInstalled();
                            }),
                    ]),

                Section::make(__('setting.log_nightwatch'))
                    ->visible(function (Get $get): bool {
                        return in_array(LogChannel::Nightwatch->value, $get('logging.channels.stack.channels'))
                            && $this->nightwatchInstalled();
                    })->schema(Settings\Forms\Log\NightwatchForm::make()),

                Section::make(__('setting.log_papertrail'))
                    ->visible(function (Get $get): bool {
                        return in_array(LogChannel::Papertrail->value, $get('logging.channels.stack.channels'));
                    })
                    ->collapsible()
                    ->schema(Settings\Forms\Log\PapertailForm::make()),

                Section::make(__('setting.log_slack'))
                    ->visible(function (Get $get): bool {
                        return in_array(LogChannel::Slack->value, $get('logging.channels.stack.channels'));
                    })
                    ->collapsible()
                    ->schema(Settings\Forms\Log\SlackForm::make()),
            ]);
    }

    /**
     * @throws ValidationException
     */
    public function update(): void
    {
        abort_unless(user_can(LogPermission::Edit), Response::HTTP_FORBIDDEN);

        $this->validate();

        try {
            $logs = [];
            foreach (Arr::dot($this->form->getState()['logging']) as $value) {
                $logs[] = $value;
            }

            // store array channels
            Setting::set('logging.channels.stack.channels', $logs);

            // update specific setting for Nightwatch
            $nightwatch = Arr::dot($this->form->getState()['nightwatch'] ?? []);
            foreach ($nightwatch as $key => $value) {
                $key = sprintf('%s.%s', LogChannel::Nightwatch->value, $key);

                if ($key === 'nightwatch.enabled' && $value === false) {
                    Setting::set($key, false);

                    break;
                }

                Setting::set($key, $value);
            }

            event(new SettingUpdated);

            Notification::make('log_updated')
                ->title(__('setting.log_updated'))
                ->success()
                ->send();
        } catch (Exception $e) {
            Logger::error($e);

            Notification::make('log_not_updated')
                ->title(__('setting.log_not_updated'))
                ->danger()
                ->send();
        }
    }

    private function nightwatchInstalled(): bool
    {
        return class_exists('Laravel\Nightwatch\NightwatchServiceProvider');
    }
}
