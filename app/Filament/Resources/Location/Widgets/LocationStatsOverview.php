<?php

namespace App\Filament\Resources\Location\Widgets;

use App\Models\Location\Country;
use App\Models\Location\District;
use App\Models\Location\Region;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class LocationStatsOverview extends BaseWidget
{
    protected ?string $pollingInterval = null;

    protected function getStats(): array
    {
        return [
            BaseWidget\Stat::make(__('location.country'), Country::query()->count())
                ->description(__('location.inactive', [
                    'count' => Country::query()->whereIsActive(false)->count(),
                ])),

            BaseWidget\Stat::make(__('location.region'), Region::query()->count())
                ->description(__('location.inactive', [
                    'count' => Region::query()->whereIsActive(false)->count(),
                ])),

            BaseWidget\Stat::make(__('location.district'), District::query()->count())
                ->description(__('location.inactive', [
                    'count' => District::query()->whereIsActive(false)->count(),
                ])),
        ];
    }
}
