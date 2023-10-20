<?php

namespace App\Filament\Resources\Location\RegionResource\Pages;

use App\Filament\Resources\Location\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;

class ManageRegions extends ManageRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(Auth::user()->can('Create region')),
        ];
    }

    public function mount(): void
    {
        abort_unless(Auth::user()->can('View region'), 403);
    }
}
