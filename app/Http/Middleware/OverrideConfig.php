<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class OverrideConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasSetting = Cache::rememberForever('has_setting', function (): bool {
            return Schema::hasTable((new Setting)->getTable());
        });

        if (! $hasSetting) {
            return $next($request);
        }

        Setting::getAll()->each(function (Setting $setting): void {
            $value = $setting->value;
            if ($value === '1' || $value === '0') {
                $value = boolval($value);
            }

            Config::set($setting->key, $value);
        });

        return $next($request);
    }
}
