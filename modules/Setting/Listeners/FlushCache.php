<?php

namespace Modules\Setting\Listeners;

use Illuminate\Support\Facades\Cache;
use Modules\Setting\Events\SettingUpdated;

class FlushCache
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SettingUpdated $event): void
    {
        if (config('setting.cache')) {
            Cache::flush(config('setting.cache_key'));
        }
    }
}
