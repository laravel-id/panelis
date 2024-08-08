<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\LogChannel;
use App\Models\Setting;
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
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log as Logger;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class Log extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 7;

    public array $logging;

    public array $larabug;

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

                        // special test for Larabug
                        if (in_array('larabug', config('logging.channels.stack.channels'))) {
                            Artisan::call('larabug:test');
                        }

                        Notification::make('log_test_sent')
                            ->title(__('setting.log_test_sent'))
                            ->success()
                            ->send();

                    } catch (\Exception) {
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
        return Auth::user()->can('ViewLogSetting');
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
                    'slack' => [
                        'username' => config('logging.channels.slack.username'),
                        'url' => config('logging.channels.slack.url'),
                        'level' => config('logging.channels.slack.level'),
                    ],
                    'papertrail' => [
                        'level' => config('logging.channels.papertrail.level'),
                        'url' => config('logging.channels.papertrail.url'),
                        'port' => config('logging.channels.papertrail.port', 514),
                    ],
                ],
            ],

            'larabug' => [
                'login_key' => config('larabug.login_key'),
                'project_key' => config('larabug.project_key'),
                'environments' => config('larabug.environments'),
            ],

            'isButtonDisabled' => ! Auth::user()->can('ViewLogSetting'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->disabled(! Auth::user()->can('UpdateLogSetting'))
            ->schema([
                Section::make(__('setting.log'))
                    ->description(__('setting.log_section_description'))
                    ->schema([
                        CheckboxList::make('logging.channels.stack.channels')
                            ->label(__('setting.log_channel'))
                            ->descriptions(LogChannel::descriptions())
                            ->live()
                            ->required()
                            ->options(LogChannel::options()),
                    ]),

                Section::make(__('setting.log_larabug'))
                    ->visible(fn (Get $get): bool => in_array('larabug', $get('logging.channels.stack.channels')))
                    ->collapsible()
                    ->schema(Settings\Forms\Log\LarabugForm::make()),

                Section::make(__('setting.log_papertrail'))
                    ->visible(function (Get $get): bool {
                        return in_array('papertrail', $get('logging.channels.stack.channels'));
                    })
                    ->collapsible()
                    ->schema(Settings\Forms\Log\PapertailForm::make()),

                Section::make(__('setting.log_slack'))
                    ->visible(function (Get $get): bool {
                        return in_array('slack', $get('logging.channels.stack.channels'));
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
        abort_unless(Auth::user()->can('UpdateLogSetting'), Response::HTTP_FORBIDDEN);

        $this->validate();

        try {
            $logs = [];
            foreach (Arr::dot($this->form->getState()['logging']) as $value) {
                $logs[] = $value;
            }

            // store array channels
            Setting::set('logging.channels.stack.channels', $logs);

            // larabug environments
            foreach ($this->form->getState()['larabug'] as $key => $value) {
                Setting::set('larabug.'.$key, $value);
            }

            event(new SettingUpdated);

            Notification::make('log_updated')
                ->title(__('setting.log_updated'))
                ->success()
                ->send();
        } catch (\Exception $e) {
            Logger::error($e);

            Notification::make('log_not_updated')
                ->title(__('setting.log_not_updated'))
                ->danger()
                ->send();
        }
    }
}
