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

class GeneralForm
{
    public static function make(): array
    {
        return [
            TextInput::make('app.url')
                ->label(__('setting.general.url'))
                ->url()
                ->required(),

            TextInput::make('app.name')
                ->label(__('setting.general.brand'))
                ->required()
                ->minValue(2)
                ->maxValue(50),

            Textarea::make('app.description')
                ->label(__('setting.general.description'))
                ->rows(5)
                ->nullable(),

            TagsInput::make('app.locales')
                ->label(__('setting.general.available_locales'))
                ->hintColor('primary')
                ->hint(
                    str(__('setting.general.locale_list_hint'))
                        ->inlineMarkdown()
                        ->toHtmlString()
                )
                ->live()
                ->required(),

            Radio::make('app.locale')
                ->label(__('setting.general.default_locale'))
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
                ->label(__('setting.general.email'))
                ->nullable()
                ->email()
                ->live(onBlur: true)
                ->maxValue(100),

            Toggle::make('app.email_as_sender')
                ->label(__('setting.general.email_as_sender'))
                ->default(0)
                ->disabled(function (Get $get): bool {
                    $email = $get('app.email');

                    return empty($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL);
                }),
        ];
    }
}
