<?php

namespace App\Models\Enums;

enum DatabasePeriod: string
{
    case Daily = 'daily';

    public static function options(): array
    {
        return collect(DatabasePeriod::cases())
            ->mapWithKeys(function ($case): array {
                $case = $case->value;

                return [$case => __(sprintf('database.period_%s', $case))];
            })
            ->toArray();
    }
}
