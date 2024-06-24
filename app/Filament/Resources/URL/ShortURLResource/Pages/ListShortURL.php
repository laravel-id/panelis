<?php

namespace App\Filament\Resources\URL\ShortURLResource\Pages;

use App\Filament\Resources\URL\ShortURLResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListShortURL extends ListRecords
{
    protected static string $resource = ShortURLResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
