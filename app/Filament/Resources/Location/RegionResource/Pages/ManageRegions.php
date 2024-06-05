<?php

namespace App\Filament\Resources\Location\RegionResource\Pages;

use App\Filament\Resources\Location\RegionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Http\Response;
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
        abort_unless(config('modules.location'), Response::HTTP_NOT_FOUND);
        abort_unless(Auth::user()->can('View region'), Response::HTTP_FORBIDDEN);
    }
}
