<?php

namespace App\Filament\Resources\TranslationResource\Forms;

use App\Models\Translation;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;

class ExportForm
{
    public static function make(): array
    {
        return [
            Radio::make('locale')
                ->label(__('translation.locale'))
                ->required()
                ->options(TranslationForm::getLocales()),

            Select::make('groups')
                ->label(__('translation.group'))
                ->multiple()
                ->searchable()
                ->options(function (): array {
                    return Translation::query()
                        ->select('group')
                        ->groupBy('group')
                        ->pluck('group', 'group')
                        ->toArray();
                }),

            Toggle::make('is_system')
                ->label(__('translation.is_system'))
                ->inline(false)
                ->label(__('translation.system_only')),
        ];
    }
}
