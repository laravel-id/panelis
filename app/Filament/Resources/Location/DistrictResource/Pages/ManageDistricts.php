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
                ->visible(Auth::user()->can('Create district')),
        ];
    }

    public function mount(): void
    {
        abort_unless(config('modules.location'), Response::HTTP_NOT_FOUND);
        abort_unless(Auth::user()->can('View district'), Response::HTTP_FORBIDDEN);
    }
}
