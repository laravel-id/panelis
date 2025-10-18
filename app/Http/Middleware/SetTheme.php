<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetTheme
{
    private array $colors = [
        'danger' => Color::Red,
        'gray' => Color::Zinc,
        'info' => Color::Blue,
        'primary' => Color::Amber,
        'success' => Color::Green,
        'warning' => Color::Amber,
    ];

    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request):Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        FilamentColor::register(
            collect($this->colors)
                ->mapWithKeys(function (array $default, string $color): array {
                    return [$color => config(sprintf('color.%s', $color)) ?? $default];
                })
                ->toArray()
        );

        return $next($request);
    }
}
