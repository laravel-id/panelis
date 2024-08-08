<?php

namespace App\Filament\Clusters\Settings\Forms\General;

use App\Models\Setting;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;

class GeneralForm
{
    public static function make(): array
    {
        return [
            TextInput::make('app.url')
                ->label(__('setting.url'))
                ->url()
                ->required(),

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
        ];
    }
}
