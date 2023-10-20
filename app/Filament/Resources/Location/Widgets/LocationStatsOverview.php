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
            BaseWidget\Stat::make(__('Country'), Country::count())
                ->description(__(':count inactive', [
                    'count' => Country::whereIsActive(false)->count(),
                ])),

            BaseWidget\Stat::make(__('Region'), Region::count())
                ->description(__(':count Inactive', [
                    'count' => Region::whereIsActive(false)->count(),
                ])),

            BaseWidget\Stat::make(__('District'), District::count())
                ->description(__(':count Inactive', [
                    'count' => District::whereIsActive(false)->count()
                ])),
        ];
    }
}
