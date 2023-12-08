<?php

namespace App\Filament\Resources\Location\DistrictResource\Pages;

use App\Filament\Resources\Location\DistrictResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ManageDistricts extends ManageRecords
{
    protected static string $resource = DistrictResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(Auth::user()->can('CreateDistrictLocation')),
        ];
    }

    public function mount(): void
    {
        abort_unless(config('module.location', false), Response::HTTP_NOT_FOUND);
        abort_unless(Auth::user()->can('ViewDistrictLocation'), Response::HTTP_FORBIDDEN);
    }
}
