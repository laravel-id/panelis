<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Symfony\Component\HttpFoundation\Response;

class UserSetting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! empty($request->user())) {
            Setting::getByUser(Auth::id())->each(function (Setting $setting): void {
                // override config from db with user's
                Config::set($setting->key, $setting->value);

                if ($setting->key === 'app.locale') {
                    app()->setLocale($setting->value);
                }
            });
        }

        return $next($request);
    }
}
