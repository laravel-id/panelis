<?php

namespace Modules\Setting\Panel\Clusters\Settings\Forms\Cache;

use Filament\Forms\Components\CheckboxList;
use Modules\Setting\Panel\Clusters\Settings\Enums\CacheDriver;

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
