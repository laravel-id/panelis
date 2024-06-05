<?php

namespace App\Filament\Resources\Misc\TodoResource\Widgets;

use App\Models\Misc\Todo;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class TodoStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('All todos', Todo::count()),
            Stat::make(__('New todos'), Todo::whereStatus('new')->count()),
            Stat::make(__('In progress'), Todo::whereStatus('in progress')->count()),
            Stat::make(__('Completed'), Todo::whereStatus('completed')->count()),
            Stat::make(__('Pending'), Todo::whereStatus('pending')->count()),
            Stat::make(__('Archived'), Todo::whereStatus('archived')->count()),
        ];
    }
}
