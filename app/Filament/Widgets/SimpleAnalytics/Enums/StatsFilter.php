<?php

namespace App\Filament\Widgets\SimpleAnalytics\Enums;

enum StatsFilter: string
{
    case Default = 'default';

    case Today = 'today';

    case Week = 'week';

    case Month = 'month';

    public function label(): string
    {
        return __(sprintf('widget.sa_filter_%s', $this->value));
    }

    public function filter(): string
    {
        return match ($this->value) {
            '', 'default' => 'today-7d',
            'today' => 'today-1d',
            'week' => 'today-7d',
            'month' => 'today-30d',
        };
    }
}
