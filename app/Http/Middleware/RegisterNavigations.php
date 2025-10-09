<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterNavigations
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        Filament::serving(function () {
            Filament::registerNavigationItems([
                NavigationItem::make(__('ui.source_code'))
                    ->sort(200)
                    ->group(__('setting.navigation'))
                    ->url('https://github.com/laravel-id/panelis')
                    ->activeIcon('heroicon-s-code-bracket')
                    ->icon('heroicon-o-code-bracket'),

            ]);
        });

        return $next($request);
    }
}
