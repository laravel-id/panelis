<?php

namespace App\Providers;

use App\Models\Module;
use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::unguard();

        $this->registerModules();
        $this->registerCustomNavigations();
    }

    private function registerCustomNavigations()
    {
        Filament::serving(function () {
            Filament::registerNavigationItems([
                NavigationItem::make(__('common.source_code'))
                    ->sort(200)
                    ->group(__('setting.navigation'))
                    ->url('https://github.com/laravel-id/panelis')
                    ->activeIcon('heroicon-s-code-bracket')
                    ->icon('heroicon-o-code-bracket'),

            ]);
        });
    }

    private function registerModules()
    {
        if (Schema::hasTable((new Module)->getTable())) {
            $modules = Cache::remember('modules', now()->addHour(), function () {
                return Module::select('name', 'is_enabled')
                    ->get();
            });

            foreach ($modules as $module) {
                config()->set(sprintf('modules.%s', strtolower($module->name)), $module->is_enabled);
            }
        }
    }
}
