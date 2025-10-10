<?php

namespace App\Filament\Resources\TranslationResource\Forms;

use App\Actions\Translation\ImportFromFiles;
use Filament\Forms\Components\Select;

class ImportFromFileForm
{
    public static function schema(): array
    {
        return [
            Select::make('files')
                ->label(__('translation.files'))
                ->options(ImportFromFiles::getAllFiles())
                ->searchable()
                ->multiple(),
        ];
    }
}
