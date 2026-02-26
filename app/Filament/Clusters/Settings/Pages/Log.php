<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\LogChannel;
use App\Filament\Clusters\Settings\Enums\LogPermission;
use App\Filament\Clusters\Settings\Forms\Log\NightwatchForm;
use App\Filament\Clusters\Settings\Forms\Log\PapertailForm;
use App\Filament\Clusters\Settings\Forms\Log\SlackForm;
use App\Models\Setting;
use BackedEnum;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Log extends Page implements HasForms
{
    use InteractsWithForms;
    use Settings\Traits\AddUpdateButton;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::DocumentText;

    protected string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 6;

    public array $logging;

    public array $nightwatch = [];

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_log')
                ->label(__('setting.log.btn.test'))
                ->modalWidth(Width::Medium)
                ->schema([
                    Textarea::make('message')
                        ->label(__('setting.log.message'))
                        ->rows(4)
                        ->required(),

                    Toggle::make('notification')
                        ->label(__('setting.log.send_as_notification'))
                        ->default(config('logging.enable_notification')),
                ])
                ->action(function (array $data): void {
                    try {
                        Logger::debug($data['message'] ?? 'Testing log');

                        Notification::make('log.test_sent')
                            ->title(__('setting.log.test_sent'))
                            ->success()
                            ->send();

                        if ($data['notification'] ?? false) {
                            Notification::make('log.notification')
                                ->title(__('setting.log.label'))
                                ->body($data['message'])
                                ->danger()
                                ->sendToDatabase(Auth::user());
                        }
                    } catch (Exception) {
                    }
                }),
        ];
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting.log.label');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.log.navigation');
    }

    public static function canAccess(): bool
    {
        return user_can(LogPermission::Browse);
    }

    public function mount(): void
    {
        $logging = config('logging.channels.stack');

        $channel = 'logging.channels.stack.channels';
        $default = Setting::get($channel);
        if (empty($default)) {
            Setting::set($channel, [LogChannel::Single->value]);
        }

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

                'enable_notification' => config('logging.enable_notification'),
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

    public function form(Schema $schema): Schema
    {
        return $schema
            ->disabled(user_cannot(LogPermission::Edit))
            ->components([
                Section::make(__('setting.log.label'))
                    ->description(__('setting.log.section_description'))
                    ->schema([
                        Toggle::make('logging.enable_notification')
                            ->label(__('setting.log.enable_notification'))
                            ->helperText(__('setting.log.notification_helper'))
                            ->required(),

                        CheckboxList::make('logging.channels.stack.channels')
                            ->label(__('setting.log.channel'))
                            ->live()
                            ->required()
                            ->options(LogChannel::class)
                            ->enum(LogChannel::class)
                            ->disableOptionWhen(function (string $value): bool {
                                return $value === LogChannel::Nightwatch->value && ! $this->nightwatchInstalled();
                            }),
                    ]),

                Section::make(__('setting.log.nightwatch'))
                    ->visible(function (Get $get): bool {
                        return in_array(LogChannel::Nightwatch, $get('logging.channels.stack.channels'))
                            && $this->nightwatchInstalled();
                    })->schema(NightwatchForm::schema()),

                Section::make(__('setting.log.papertrail'))
                    ->visible(function (Get $get): bool {
                        return in_array(LogChannel::Papertrail, $get('logging.channels.stack.channels'));
                    })
                    ->collapsible()
                    ->schema(PapertailForm::schema()),

                Section::make(__('setting.log.slack'))
                    ->visible(function (Get $get): bool {
                        return in_array(LogChannel::Slack, $get('logging.channels.stack.channels'));
                    })
                    ->collapsible()
                    ->schema(SlackForm::schema()),
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
            $state = Arr::dot($this->form->getState()['logging']);

            $channels = array_map(function (LogChannel $channel) {
                return $channel->value;
            }, Arr::dot($this->form->getState()['logging']['channels']['stack']));
            Setting::set('logging.channels.stack.channels', array_values($channels));

            if ($enableNotification = data_get($state, 'enable_notification')) {
                Setting::set('logging.enable_notification', $enableNotification);
                unset($state['enable_notification']);
            }

            foreach ($state as $key => $value) {
                if (str_starts_with($key, 'channels.stack.channels')) {
                    continue;
                }

                Setting::set('logging.'.$key, $value);
            }

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

            Notification::make('log.updated')
                ->title(__('filament-actions::edit.single.notifications.saved.title'))
                ->success()
                ->send();
        } catch (Exception $e) {
            Logger::error($e);

            Notification::make('log.not_updated')
                ->title(__('setting.log.not_updated'))
                ->danger()
                ->send();
        }
    }

    private function nightwatchInstalled(): bool
    {
        return class_exists('Laravel\Nightwatch\NightwatchServiceProvider');
    }
}
