<?php

namespace App\Http\Middleware;

use App\Models\Module;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\Response;

class RegisterModules
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasModule = Cache::rememberForever('has_module', function (): bool {
            return Schema::hasTable((new Module)->getTable());
        });

        if (! $hasModule) {
            return $next($request);
        }

        $modules = Cache::remember('modules', now()->addHour(), function () {
            return Module::query()->select('name', 'is_enabled')
                ->get();
        });

        $modules->each(function (Module $module) {
            Config::set(sprintf('modules.%s', strtolower($module->name)), $module->is_enabled);
        });

        return $next($request);
    }
}
