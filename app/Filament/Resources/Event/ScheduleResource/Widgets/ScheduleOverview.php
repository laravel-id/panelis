<?php

namespace app\Filament\Resources\Event\ScheduleResource\Widgets;

use App\Models\Event\Schedule;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ScheduleOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $now = now(get_timezone());

        return [
            Stat::make(
                __('event.schedule_widget_current_month'),
                Schedule::query()
                    ->whereDate('started_at', '>=', $now->startOfMonth())
                    ->whereDate('started_at', '<=', $now->endOfMonth())
                    ->count(),

            )
                ->description(__('event.schedule_widget_this_month_description')),

            Stat::make(
                __('event.schedule_widget_next_month'),
                Schedule::query()
                    ->whereDate('started_at', '>=', $now->addMonth()->startOfMonth())
                    ->whereDate('started_at', '<=', $now->addMonth()->endOfMonth())
                    ->count(),
            )
                ->description(__('event.schedule_widget_next_month_description')),

            Stat::make(
                __('event.schedule_widget_this_year'),
                Schedule::query()
                    ->whereYear('started_at', $now->year)
                    ->count(),
            )
                ->description(__('event.schedule_widget_this_year_description')),
        ];
    }
}
