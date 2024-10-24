<?php

namespace app\Filament\Resources\Event\ScheduleResource\Widgets;

use App\Models\Event\Schedule;
use Carbon\CarbonImmutable;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ScheduleOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $now = CarbonImmutable::now(get_timezone());

        $pastMonth = Schedule::query()
            ->whereDate('started_at', '>=', $now->subMonth()->startOfMonth())
            ->whereDate('started_at', '<=', $now->subMonth()->endOfMonth())
            ->count();

        $currentMonth = Schedule::query()
            ->whereDate('started_at', '>=', $now->startOfMonth())
            ->whereDate('started_at', '<=', $now->endOfMonth())
            ->count();

        $nextMonth = Schedule::query()
            ->whereDate('started_at', '>=', $now->addMonth()->startOfMonth())
            ->whereDate('started_at', '<=', $now->addMonth()->endOfMonth())
            ->count();

        return [
            Stat::make(__('event.schedule_widget_current_month'), $currentMonth)
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->description(__('event.schedule_widget_this_month_description', [
                    'month' => $now->translatedFormat('F'),
                ]))
                ->descriptionIcon(sprintf('heroicon-m-arrow-trending-%s', $currentMonth > $pastMonth ? 'up' : 'down'))
                ->color($currentMonth > $pastMonth ? 'success' : 'danger'),

            Stat::make(__('event.schedule_widget_next_month'), $nextMonth)
                ->description(__('event.schedule_widget_next_month_description', [
                    'month' => $now->addMonth()->translatedFormat('F'),
                ]))
                ->descriptionIcon(sprintf('heroicon-m-arrow-trending-%s', $nextMonth > $currentMonth ? 'up' : 'down'))
                ->color($nextMonth > $currentMonth ? 'success' : 'danger'),

            Stat::make(
                __('event.schedule_widget_this_year'),
                Schedule::query()
                    ->whereYear('started_at', $now->year)
                    ->count(),
            )
                ->description(__('event.schedule_widget_this_year_description', [
                    'year' => $now->year,
                ]))
                ->chart(
                    Schedule::countPerMonth()
                        ->map(fn (Schedule $month): int => $month->count)
                        ->all(),
                ),
        ];
    }
}
