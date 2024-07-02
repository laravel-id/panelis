<?php

namespace App\Filament\Resources\SubscriberResource\Enums;

use App\Models\Enums\HasOption;

enum SubscriberPeriod: string implements HasOption
{
    case Weekly = 'weekly';

    case Monthly = 'monthly';

    public static function options(): array
    {
        return collect(SubscriberPeriod::cases())
            ->mapWithKeys(fn(SubscriberPeriod $case): array => [$case->value => $case->label()])
            ->toArray();
    }

    public function label(): string
    {
        return __("subscriber.period_{$this->value}");
    }
}