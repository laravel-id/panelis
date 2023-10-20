<?php

namespace App\Filament\Resources\Location\Widgets;

use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Region;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class LocationStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            BaseWidget\Stat::make(__('Country'), Country::count()),
            BaseWidget\Stat::make(__('Region'), Region::count()),
            BaseWidget\Stat::make(__('District'), District::count()),
        ];
    }
}
