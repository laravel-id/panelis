<?php

namespace App\Listeners\Setting;

use App\Events\SettingUpdated;
use Illuminate\Support\Facades\Cache;

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
        Cache::flush(config('setting.cache_key'));
    }
}
