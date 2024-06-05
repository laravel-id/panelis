<?php

namespace App\Filament\Resources\Location\CountryResource\Pages;

use App\Filament\Resources\Location\CountryResource;
use App\Filament\Resources\Location\Widgets\LocationStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class ManageCountries extends ManageRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(Auth::user()->can('Create country')),
        ];
    }

    public function mount(): void
    {
        abort_unless(config('modules.location'), Response::HTTP_NOT_FOUND);
        abort_unless(Auth::user()->can('View country'), Response::HTTP_FORBIDDEN);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LocationStatsOverview::class,
        ];
    }
}
