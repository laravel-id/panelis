<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings;
use App\Models\Enums\LogChannel;
use App\Models\Enums\LogLevel;
use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Enums\MaxWidth;
use Illuminate\Support\Arr;

class Log extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 7;

    public array $logging;

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
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('send_log')
                ->label(__('setting.log_button_test'))
                ->modalWidth(MaxWidth::Medium)
                ->form([
                    Textarea::make('message')
                        ->label(__('setting.log_message')),
                ])
                ->action(function (array $data): void {
                    try {
                        \Illuminate\Support\Facades\Log::debug($data['message'] ?? 'Testing log');

                        Notification::make('log_test_sent')
                            ->title(__('setting.log_test_sent'))
                            ->success()
                            ->send();

                    } catch (\Exception) {
                    }
                }),
        ];
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('setting.log'))
                ->description(__('setting.log_info'))
                ->schema([
                    CheckboxList::make('logging.channels.stack.channels')
                        ->descriptions(LogChannel::descriptions())
                        ->live()
                        ->required()
                        ->options(LogChannel::options()),
                ]),

            Section::make(__('setting.log_slack'))
                ->visible(function (Get $get): bool {
                    return in_array('slack', $get('logging.channels.stack.channels'));
                })
                ->schema([
                    Select::make('logging.channels.slack.level')
                        ->label(__('setting.log_level'))
                        ->options(LogLevel::options())
                        ->searchable()
                        ->required(),

                    TextInput::make('logging.channels.slack.url')
                        ->label(__('setting.slack_webhook_url'))
                        ->hint(
                            str(__('setting.slack_webhook_hint'))
                                ->inlineMarkdown()
                                ->toHtmlString()
                        )
                        ->url()
                        ->required(),

                    TextInput::make('logging.channels.slack.username')
                        ->label(__('setting.slack_username'))
                        ->string()
                        ->required(),
                ]),

            Section::make(__('setting.log_papertrail'))
                ->visible(function (Get $get): bool {
                    return in_array('papertrail', $get('logging.channels.stack.channels'));
                })
                ->description(__('setting.log_papertrail_info'))
                ->schema([
                    Select::make('logging.channels.papertrail.level')
                        ->options(LogLevel::options())
                        ->searchable()
                        ->required(),

                    TextInput::make('logging.channels.papertrail.url')
                        ->label(__('setting.log_papertrail_url'))
                        ->url()
                        ->required(),

                    TextInput::make('logging.channels.papertrail.port')
                        ->label(__('setting.log_papertrail_port'))
                        ->numeric()
                        ->required(),
                ]),
        ]);
    }

    public function update(): void
    {
        $this->validate();

        try {
            $stacks = [];
            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                if (str_contains($key, 'logging.channels.stack.channels')) {
                    $stacks[] = $value;

                    continue;
                }

                Setting::updateOrCreate(compact('key'), compact('value'));
            }

            // store array channels
            Setting::updateOrCreate(['key' => 'logging.channels.stack.channels'], ['value' => $stacks]);

            Notification::make('log_updated')
                ->title(__('setting.log_updated'))
                ->success()
                ->send();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error($e);

            Notification::make('log_not_updated')
                ->title(__('setting.log_not_updated'))
                ->danger()
                ->send();
        }
    }
}