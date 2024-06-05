<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class General extends Page implements HasForms, Settings\HasUpdateableForm
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog';

    protected static string $view = 'filament.clusters.settings.pages.setting';

    protected static ?string $cluster = Settings::class;

    public array $app;

    public function getTitle(): string|Htmlable
    {
        return __('setting.general');
    }

    public static function getNavigationLabel(): string
    {
        return __('setting.general');
    }

    public function mount(): void
    {
        $this->form->fill([
            'app' => [
                'debug' => config('app.debug'),
                'name' => config('app.name'),
                'description' => config('app.description'),
                'locales' => config('app.locales', [config('app.locale')]),
                'locale' => Setting::get('app.locale', config('app.locale')),
                'email' => config('app.email'),
                'email_as_sender' => config('app.email_as_sender'),
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        $locales = collect(config('app.locales'))
            ->mapWithKeys(function ($locale): array {
                return [$locale => LanguageSwitch::make()->getLabel($locale)];
            })
            ->toArray();

        // no locales in database setting
        // use default locale from Laravel
        if (empty($locales)) {
            $locales[config('app.locale')] = LanguageSwitch::make()->getLabel(config('app.locale'));
        }

        return $form->schema([
            Section::make(__('setting.general'))
                ->description(__('setting.general_info'))
                ->schema([
                    TextInput::make('app.name')
                        ->label(__('setting.brand'))
                        ->required()
                        ->minValue(2)
                        ->maxValue(50),

                    Textarea::make('app.description')
                        ->label(__('setting.description'))
                        ->rows(5)
                        ->nullable(),

                    TagsInput::make('app.locales')
                        ->label(__('setting.available_locales'))
                        ->hintColor('primary')
                        ->hint(function (): Htmlable {
                            return new HtmlString(__('setting.locale_list_hint', [
                                'link' => 'https://en.wikipedia.org/wiki/List_of_ISO_639_language_codes',
                            ]));
                        })
                        ->live()
                        ->required(),

                    Radio::make('app.locale')
                        ->label(__('setting.default_locale'))
                        ->default(Setting::get('app.locale'))
                        ->required()
                        ->options(function (Get $get): array {
                            $locales = $get('app.locales');
                            if (! empty($locales)) {
                                return array_combine($locales, array_map(function ($locale): string {
                                    return LanguageSwitch::make()->getLabel($locale);
                                }, $locales));
                            }

                            return [];
                        }),

                    TextInput::make('app.email')
                        ->label(__('setting.email'))
                        ->nullable()
                        ->email()
                        ->live(onBlur: true)
                        ->maxValue(100),

                    Toggle::make('app.email_as_sender')
                        ->label(__('setting.email_as_sender'))
                        ->default(0)
                        ->disabled(function (Get $get): bool {
                            $email = $get('app.email');

                            return empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL);
                        }),
                ]),

            Section::make(__('setting.development_mode'))
                ->collapsed()
                ->schema([
                    Toggle::make('app.debug')
                        ->label(__('setting.app_debug'))
                        ->helperText(fn (): ?string => app()->isProduction() ? __('setting.debug_recommendation') : null),
                ]),
        ])->disabled(config('app.demo'));
    }

    public function update(): void
    {
        $this->validate();

        if (config('app.demo')) {
            return;
        }

        $state = $this->form->getState();
        foreach ($state['app'] as $key => $value) {
            $key = sprintf('app.%s', $key);
            if ($value === false) {
                $value = '0';
            }

            if ($key === 'app.email_as_sender' && data_get($state, 'app.email_as_sender') === true) {
                Setting::set('mail.from.address', data_get($state, 'app.email'));
            }

            Setting::set($key, $value);
        }

        event(new SettingUpdated);

        Notification::make('general_updated')
            ->title(__('setting.general_updated'))
            ->success()
            ->send();
    }
}
