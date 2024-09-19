<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CacheResponse
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! boolval(config('response.cache', false)) || ! app()->isProduction()) {
            return $next($request);
        }

        $key = 'response.'.sha1($request->fullUrl());
        if (Cache::has($key)) {
            return response(Cache::get($key));
        }

        $response = $next($request);

        Cache::put($key, (string) $response->getContent(), now()->addHour());

        return $response;
    }
}
