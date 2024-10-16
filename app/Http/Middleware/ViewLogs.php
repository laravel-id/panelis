<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ViewLogs
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        abort_if(
            boolean: empty($request->user())
                || ! $request->user()?->is_root
                || ! $request->user()->can('ViewLogs'),

            code: Response::HTTP_FORBIDDEN,
        );

        return $next($request);
    }
}
