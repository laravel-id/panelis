<?php

namespace App\Filament\Clusters\Settings\Forms\Cache;

use App\Filament\Clusters\Settings\Enums\CacheDriver;
use Filament\Forms\Components\CheckboxList;

class FailoverForm
{
    public static function schema(): array
    {
        return [
            CheckboxList::make('cache.stores.failover.stores')
                ->options(CacheDriver::class),
        ];
    }
}
