<?php

namespace Modules\Setting\Panel\Clusters\Settings\Enums;

enum CachePermission: string
{
    case Browse = 'BrowseCacheSetting';

    case Edit = 'EditCacheSetting';

    case Flush = 'FlushCacheSetting';
}
