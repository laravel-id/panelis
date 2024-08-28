<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\EmailVerificationPrompt;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Filament\Pages\Dashboard;
use App\Filament\Widgets\SimpleAnalytics\ReferralChart as SimpleAnalyticsReferralChart;
use App\Filament\Widgets\SimpleAnalytics\StatsChart as SimpleAnalyticsStatsChart;
use App\Filament\Widgets\SimpleAnalytics\TopPage as SimpleAnalyticsTopPage;
use App\Http\Middleware\RegisterModules;
use App\Http\Middleware\SetTheme;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\PanelProvider;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use pxlrbt\FilamentEnvironmentIndicator\EnvironmentIndicatorPlugin;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $path = 'admin';

        return $panel
            ->default()
            ->id('admin')

            // uncomment to set different path
            ->path($path)
            ->plugins([
                //TodoPlugin::make(),
                EnvironmentIndicatorPlugin::make()
                    ->visible(! app()->isProduction()),
            ])

            ->navigationItems([
                NavigationItem::make(__('navigation.website'))
                    ->icon('heroicon-o-globe-alt')
                    ->url(config('app.url'), shouldOpenInNewTab: true),

                NavigationItem::make(__('event.schedule_create'))
                    // ->url(CreateSchedule::getUrl())
                    ->isActiveWhen(fn (): bool => request()->routeIs('filament.admin.resources.event.schedules.create'))
                    ->url(sprintf('/%s/event/schedules/create', $path))
                    ->group(__('navigation.event')),
            ])
            ->navigationGroups([
                NavigationGroup::make(__('navigation.event'))
                    ->icon('heroicon-o-calendar-days'),

                NavigationGroup::make(__('navigation.location'))
                    ->icon('heroicon-o-map')
                    ->collapsed(),

                NavigationGroup::make(__('navigation.user'))
                    ->icon('heroicon-o-user-group'),

                NavigationGroup::make(__('navigation.system'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed(),
            ])

            ->unsavedChangesAlerts()

            //->registration(Register::class)
            ->login(Login::class)
            ->passwordReset(RequestPasswordReset::class)
            ->profile(EditProfile::class)
            ->emailVerification(EmailVerificationPrompt::class)
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverClusters(in: app_path('Filament/Clusters'), for: 'App\\Filament\\Clusters')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                SimpleAnalyticsStatsChart::class,
                SimpleAnalyticsReferralChart::class,
                SimpleAnalyticsTopPage::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,

                // custom middlewares
                RegisterModules::class,
                SetTheme::class,
            ])
            ->authMiddleware([
                Authenticate::class,

                // custom middlewares
            ]);
    }
}
