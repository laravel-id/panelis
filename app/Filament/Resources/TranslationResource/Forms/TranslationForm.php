<?php

namespace App\Filament\Resources\TranslationResource\Forms;

use App\Models\Translation;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;

class TranslationForm
{
    public static function getLocales(): array
    {
        $key = 'app.locales';
        if (empty(config($key))) {
            config()->set($key, [config('app.locale', default: 'en')]);
        }

        return collect(config($key))
            ->mapWithKeys(function ($locale): array {
                return [$locale => LanguageSwitch::make()->getLabel($locale)];
            })
            ->toArray();
    }

    public static function schema(): array
    {
        return [
            TextInput::make('group')
                ->label(__('translation.group'))
                ->autocomplete(false)
                ->autofocus()
                ->datalist(function (): array {
                    return Translation::orderBy('group')
                        ->groupBy('group')
                        ->pluck('group')
                        ->toArray();
                })
                ->helperText(function (?string $operation, ?Translation $line): ?string {
                    if ($operation === 'edit' && ! $line->is_system) {
                        return __('translation.group_change_warning');
                    }

                    return null;
                })
                ->disabled(fn (?Translation $line): bool => $line?->is_system ?? false)
                ->required()
                ->alphaDash(),

            TextInput::make('key')
                ->label(__('translation.key'))
                ->helperText(function (?string $operation, ?Translation $line): ?string {
                    if ($operation === 'edit' && ! $line->is_system) {
                        return __('translation.key_change_warning');
                    }

                    return null;
                })
                ->autocomplete(false)
                ->disabled(fn (?Translation $line): bool => $line?->is_system ?? false)
                ->required(),

            KeyValue::make('text')
                ->label(__('translation.text'))
                ->addActionLabel(__('translation.btn.add_line'))
                ->keyLabel(__('translation.lang'))
                ->valueLabel(__('translation.line'))
                ->default(function (): array {
                    if (! empty(config('app.locales'))) {
                        return array_fill_keys(config('app.locales'), '');
                    }

                    return [config('app.locale') => ''];
                })
                ->helperText(__('translation.helper_locales_generated_setting'))
                ->required(),
        ];
    }
}
