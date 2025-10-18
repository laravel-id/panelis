<?php

namespace App\Filament\Resources\Location\CountryResource\Pages;

use App\Filament\Resources\Location\CountryResource;
use App\Filament\Resources\Location\CountryResource\Enums\CountryPermission;
use App\Filament\Resources\Location\Widgets\LocationStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Symfony\Component\HttpFoundation\Response;

class ManageCountries extends ManageRecords
{
    protected static string $resource = CountryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(user_can(CountryPermission::Add)),
        ];
    }

    public function mount(): void
    {
        abort_unless(config('module.location', false), Response::HTTP_NOT_FOUND);

        abort_unless(
            user_can(CountryPermission::Browse) && user_can(CountryPermission::Add),
            Response::HTTP_FORBIDDEN,
        );
    }

    protected function getHeaderWidgets(): array
    {
        return [
            LocationStatsOverview::class,
        ];
    }
}
