<?php

namespace App\Filament\Clusters\Settings\Enums;

use App\Models\Enums\HasOption;

enum CacheDriver: string implements HasOption
{
    case File = 'file';

    case Database = 'database';

    case APC = 'apc';

    case Memcached = 'memcached';

    case Redis = 'redis';

    case DynamoDB = 'dynamodb';

    public static function options(): array
    {
        return collect(CacheDriver::cases())
            ->mapWithKeys(function (CacheDriver $case): array {
                return [$case->value => $case->label()];
            })
            ->toArray();
    }

    public static function descriptions(): array
    {
        return collect(CacheDriver::cases())
            ->mapWithKeys(fn (CacheDriver $case): array => [$case->value => $case->description()])
            ->toArray();
    }

    public function label(): string
    {
        return __(sprintf('setting.cache_driver_%s', $this->value));
    }

    public function description(): string
    {
        return __(sprintf('setting.cache_description_%s', $this->value));
    }
}
