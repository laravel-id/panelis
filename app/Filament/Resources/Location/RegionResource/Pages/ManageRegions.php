<?php

namespace App\Filament\Resources\Location\RegionResource\Pages;

use App\Filament\Resources\Location\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManageRegions extends ManageRecords
{
    protected static string $resource = RegionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(Auth::user()->can('CreateRegionLocation')),
        ];
    }

    public function mount(): void
    {
        abort_unless(config('module.location', false), Response::HTTP_NOT_FOUND);

        abort_unless(Auth::user()->can('ViewRegionLocation'), Response::HTTP_FORBIDDEN);
    }
}
