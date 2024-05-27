<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Events\SettingUpdated;
use App\Filament\Clusters\Settings;
use App\Models\Setting;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Arr;

class General extends Page
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

    public function mount(): void
    {
        $this->form->fill([
            'app' => [
                'name' => config('app.name'),
                'description' => config('app.description'),
                'locale' => Setting::getByKey('app.locale', config('app.locale')),
                'email' => config('app.email'),
                'email_as_sender' => config('app.email_as_sender'),
            ],
        ]);
    }

    public function form(Form $form): Form
    {
        $locales = LanguageSwitch::make()->getLocales();

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

                    Radio::make('app.locale')
                        ->label(__('setting.default_locale'))
                        ->default(Setting::getByKey('app.locale'))
                        ->required()
                        ->options(array_combine($locales, $locales)),

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

        Notification::make('general_updated')
            ->title(__('setting.general_updated'))
            ->success()
            ->send();
    }
}
