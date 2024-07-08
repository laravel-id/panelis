<?php

namespace App\Filament\Resources\Location\CountryResource\Pages;

use App\Filament\Resources\Location\CountryResource;
use App\Filament\Resources\Location\Widgets\LocationStatsOverview;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManageCountries extends ManageRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(Auth::user()->can('CreateCountryLocation')),
        ];
    }

    public function mount(): void
    {
        abort_unless(config('module.location', false), Response::HTTP_NOT_FOUND);

        abort_unless(Auth::user()->can('ViewCountryLocation'), Response::HTTP_FORBIDDEN);
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LocationStatsOverview::class,
        ];
    }
}
