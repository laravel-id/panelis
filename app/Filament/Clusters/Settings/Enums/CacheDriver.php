<?php

namespace App\Filament\Clusters\Settings\Enums;

use Filament\Support\Contracts\HasDescription;
use Filament\Support\Contracts\HasLabel;

enum CacheDriver: string implements HasDescription, HasLabel
{
    case File = 'file';

    case Database = 'database';

    case APC = 'apc';

    case Memcached = 'memcached';

    case Redis = 'redis';

    case DynamoDB = 'dynamodb';

    public function getLabel(): string
    {
        return __(sprintf('setting.cache.%s_driver', $this->value));
    }

    public function getDescription(): string
    {
        return __(sprintf('setting.cache.%s_description', $this->value));
    }
}
