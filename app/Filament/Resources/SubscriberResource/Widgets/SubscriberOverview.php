<?php

namespace App\Filament\Resources\SubscriberResource\Widgets;

use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriberOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('subscriber.widget_total'), Subscriber::query()->count())
                ->icon('heroicon-s-users'),

            Stat::make(__('subscriber.widget_total_subscribed'), Subscriber::query()->subscribed()->count())
                ->icon('heroicon-o-bookmark')
                ->color('green'),

            Stat::make(__('subscriber.widget_total_unsubscribed'), Subscriber::query()->subscribed(false)->count())
                ->icon('heroicon-o-bookmark-slash')
                ->color('gray'),
        ];
    }
}
