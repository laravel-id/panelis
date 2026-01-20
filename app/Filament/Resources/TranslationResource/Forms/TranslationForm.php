<?php

namespace App\Filament\Resources\TranslationResource\Forms;

use App\Models\Translation;
use BezhanSalleh\LanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Repeater\TableColumn;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
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

            Repeater::make('text')
                ->columnSpanFull()
                ->hiddenLabel()
                ->default(array_map(
                    fn (string $locale) => ['lang' => $locale],
                    LanguageSwitch::make()->getLocales(),
                ))
                ->afterStateHydrated(function (Field $component, mixed $state, string $operation): void {
                    if ($operation === 'create') {
                        return;
                    }

                    $locales = LanguageSwitch::make()->getLocales();

                    $defaults = collect($locales)
                        ->map(fn (string $locale) => [
                            'lang' => $locale,
                            'line' => null,
                        ])
                        ->keyBy('lang');

                    if (is_array($state) && ! empty($state)) {
                        $fromDb = collect($state)
                            ->map(fn ($line, $lang) => [
                                'lang' => $lang,
                                'line' => $line,
                            ])
                            ->keyBy('lang');

                        $defaults = $defaults->merge($fromDb);
                    }

                    $component->state(
                        $defaults->values()->toArray()
                    );
                })

                ->table([
                    TableColumn::make(__('translation.lang'))->markAsRequired(),
                    TableColumn::make(__('translation.text'))->markAsRequired(),
                ])
                ->compact()
                ->maxItems(count(LanguageSwitch::make()->getLocales()))
                ->schema([
                    Select::make('lang')
                        ->hiddenLabel()
                        ->options(function (): array {
                            return collect(LanguageSwitch::make()->getLocales())
                                ->mapWithKeys(fn (string $locale): array => [$locale => LanguageSwitch::make()->getLabel($locale)])
                                ->toArray();
                        })
                        ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                        ->required(),

                    Textarea::make('line')
                        ->hiddenLabel()
                        ->required()
                        ->rows(2),
                ]),
        ];
    }
}
