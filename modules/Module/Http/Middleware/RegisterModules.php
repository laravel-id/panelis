<?php

namespace Modules\Module\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Modules\Module\Models\Module;
use Symfony\Component\HttpFoundation\Response;

class RegisterModules
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $hasModule = Cache::rememberForever('has_module', function (): bool {
            return Schema::hasTable((new Module)->getTable());
        });

        if ($hasModule) {
            Module::query()
                ->get()
                ->each(function (Module $module): void {
                    $name = sprintf('module.%s', Str::snake($module->name));

                    Config::set($name, (bool) $module->is_enabled);
                });
        }

        return $next($request);
    }
}
