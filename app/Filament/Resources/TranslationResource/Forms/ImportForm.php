<?php

namespace App\Filament\Resources\TranslationResource\Forms;

use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;

class ImportForm
{
    private static string $disk = 'local';

    public static function make(): array
    {
        return [
            Radio::make('locale')
                ->required()
                ->label(__('translation.locale'))
                ->live()
                ->options(TranslationForm::getLocales()),

            FileUpload::make('trans')
                ->previewable(false)
                ->storeFiles(false)
                ->fetchFileInformation(false)
                ->disk(self::$disk)
                ->visibility('private')
                ->acceptedFileTypes([
                    'application/json',
                ])
                ->required(),
        ];
    }
}