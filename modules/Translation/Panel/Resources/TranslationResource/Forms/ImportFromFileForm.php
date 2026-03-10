<?php

namespace Modules\Translation\Panel\Resources\TranslationResource\Forms;

use Filament\Forms\Components\Select;
use Modules\Translation\Actions\ImportFromFiles;

class ImportFromFileForm
{
    public static function schema(): array
    {
        return [
            Select::make('files')
                ->label(__('translation::translation.files'))
                ->options(ImportFromFiles::getAllFiles())
                ->required()
                ->searchable()
                ->multiple(),
        ];
    }
}
