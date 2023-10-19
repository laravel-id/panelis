<?php

namespace App\Filament\Resources\Location\RegionResource\Pages;

use App\Filament\Resources\Location\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRegions extends ManageRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
