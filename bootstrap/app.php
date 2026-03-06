<?php

use App\Exceptions\Reporter\FilamentReporter;
use App\Http\Middleware\Panelis\UserIsRoot;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('api')
                ->prefix('callback')
                ->name('callback.')
                ->group(base_path('routes/callback.php'));

            Route::middleware(['auth', 'web', 'panelis.is_root'])
                ->name('panelis.')
                ->group(base_path('routes/panelis.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'panelis.is_root' => UserIsRoot::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->report(function (Throwable $e): void {
            app(FilamentReporter::class)->handle($e);
        });
    })->create();
