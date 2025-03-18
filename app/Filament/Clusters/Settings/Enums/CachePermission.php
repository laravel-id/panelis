<?php

namespace App\Filament\Clusters\Settings\Enums;

enum CachePermission: string
{
    case Browse = 'BrowseCacheSetting';

    case Edit = 'EditCacheSetting';

    case Flush = 'FlushCacheSetting';
}
