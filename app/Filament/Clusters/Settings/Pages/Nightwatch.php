<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Filament\Clusters\Settings\Enums\NightwatchPermission;
use App\Models\Setting;
use Exception;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;
use Symfony\Component\HttpFoundation\Response;

class Nightwatch extends Page implements Settings\HasUpdateableForm
{
    protected static ?string $navigationIcon = 'heroicon-o-moon';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    protected static ?int $navigationSort = 6;

    public array $nightwatch = [];

    public static function nightwatchInstalled(): bool
    {
        return class_exists('Laravel\Nightwatch\NightwatchServiceProvider');
    }

    public function getTitle(): string|Htmlable
    {
        return __('setting.nightwatch');
    }

    public static function getNavigationLabel(): string
    {
        return __('navigation.setting_nightwatch');
    }

    public static function canAccess(): bool
    {
        return user_can(NightwatchPermission::Browse) && self::nightwatchInstalled();
    }

    public static function shouldRegisterNavigation(): bool
    {
        return self::nightwatchInstalled();
    }

    public function mount(): void
    {
        $this->form->fill([
            'nightwatch' => [
                'enabled' => config('nightwatch.enabled'),
                'token' => config('nightwatch.token'),
                'sampling' => [
                    'requests' => config('nightwatch.sampling.requests'),
                    'commands' => config('nightwatch.sampling.commands'),
                    'exceptions' => config('nightwatch.sampling.exceptions'),
                ],
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Section::make(__('setting.nightwatch'))
                ->description(__('setting.nightwatch_section_description'))
                ->schema([
                    Checkbox::make('nightwatch.enabled')
                        ->label(__('setting.nightwatch_enabled'))
                        ->live(),

                    TextInput::make('nightwatch.token')
                        ->label(__('setting.nightwatch_token'))
                        ->password()
                        ->revealable()
                        ->required()
                        ->disabled(fn (Get $get): bool => ! $get('nightwatch.enabled')),

                    Placeholder::make('nightwatch.server')
                        ->label(__('setting.nightwatch_server'))
                        ->content(function (): string {
                            return config('nightwatch.server');
                        }),

                    Fieldset::make(__('setting.nightwatch_sampling'))
                        ->columns(3)
                        ->disabled(fn (Get $get): bool => ! $get('nightwatch.enabled'))
                        ->schema([
                            TextInput::make('nightwatch.sampling.requests')
                                ->label(__('setting.nightwatch_sampling_requests'))
                                ->default(1)
                                ->required()
                                ->numeric(),

                            TextInput::make('nightwatch.sampling.commands')
                                ->label(__('setting.nightwatch_sampling_commands'))
                                ->required()
                                ->numeric(),

                            TextInput::make('nightwatch.sampling.exceptions')
                                ->label(__('setting.nightwatch_sampling_exceptions'))
                                ->required()
                                ->numeric(),
                        ]),
                ]),
        ]);
    }

    public function update(): void
    {
        abort_unless(user_can(NightwatchPermission::Edit), Response::HTTP_FORBIDDEN);

        $this->validate();

        try {
            foreach (Arr::dot($this->form->getState()) as $key => $value) {
                Setting::updateOrCreate(compact('key'), compact('value'));
            }

            event(new SettingUpdated);

            Notification::make('nightwatch_setting_updated')
                ->title(__('setting.nightwatch_updated'))
                ->success()
                ->send();
        } catch (Exception) {
            Notification::make('nightwatch_setting_not_updated')
                ->title(__('setting.nightwatch_not_updated'))
                ->warning()
                ->send();
        }
    }
}
