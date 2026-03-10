<?php

namespace Modules\Translation\Panel\Resources\TranslationResource\Forms;

use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Modules\Translation\Models\Translation;

class ExportForm
{
    public static function schema(): array
    {
        return [
            Radio::make('locale')
                ->label(__('translation::translation.locale'))
                ->required()
                ->options(TranslationForm::getLocales()),

            Select::make('groups')
                ->label(__('translation::translation.group'))
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
                ->label(__('translation::translation.is_system'))
                ->inline(false)
                ->label(__('translation::translation.system_only')),
        ];
    }
}
