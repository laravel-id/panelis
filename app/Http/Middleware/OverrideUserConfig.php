<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class OverrideUserConfig
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Schema::hasTable((new Setting())->getTable())) {
            return $next($request);
        }

        if (! empty($request->user())) {
            foreach (Setting::getByUser($request->user()->id) as $setting) {
                $value = $setting->value;
                if ($value === '1' || $value === '0') {
                    $value = boolval($value);
                }
                Config::set($setting->key, $value);
            }
        }

        return $next($request);
    }
}
