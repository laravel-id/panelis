<?php

namespace App\Models\Enums;

enum CacheDriver: string
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
                return [$case->value => $case->getLabel()];
            })
            ->toArray();
    }

    public function getLabel(): string
    {
        return __(sprintf('setting.cache_driver_%s', $this->value));
    }
}
