<?php

namespace App\Filament\Resources\Location\Widgets;

use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Region;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class LocationStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            BaseWidget\Stat::make(__('location.country'), Country::count())
                ->description(__('location.inactive', [
                    'count' => Country::whereIsActive(false)->count(),
                ])),

            BaseWidget\Stat::make(__('location.region'), Region::count())
                ->description(__('location.inactive', [
                    'count' => Region::whereIsActive(false)->count(),
                ])),

            BaseWidget\Stat::make(__('location.district'), District::count())
                ->description(__('location.inactive', [
                    'count' => District::whereIsActive(false)->count()
                ])),
        ];
    }
}
