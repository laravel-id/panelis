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

    public static function getDescriptions(): array
    {
        return collect(CacheDriver::cases())
            ->mapWithKeys(fn (CacheDriver $case): array => [$case->value => $case->getDescription()])
            ->toArray();
    }

    public function getLabel(): string
    {
        return __(sprintf('setting.cache_driver_%s', $this->value));
    }

    public function getDescription(): string
    {
        return __(sprintf('setting.cache_description_%s', $this->value));
    }
}
