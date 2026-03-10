<?php

namespace App\Providers\Filament;

use App\Http\Middleware\RegisterNavigations;
use App\Http\Middleware\SetTheme;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Modules\Branch\Models\Branch;
use Modules\Branch\Panel\Pages\EditBranch;
use Modules\Branch\Panel\Pages\RegisterBranch;
use Modules\Module\Http\Middleware\RegisterModules;
use Modules\User\Panel\Pages\EditProfile;
use Modules\User\Panel\Pages\EmailVerificationPrompt;
use Modules\User\Panel\Pages\Login;
use Modules\User\Panel\Pages\RequestPasswordReset;
use Panelis\Support\ModuleManager;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $panel->default()
            ->id(config('panelis.id'));

        if ((bool) app('panelis')['multitenant'] ?? false) {
            $panel->tenant(Branch::class, slugAttribute: 'slug')
                ->tenantRegistration(RegisterBranch::class)
                ->tenantProfile(EditBranch::class);
        }

        foreach (ModuleManager::getResources() as $resource) {
            $panel->discoverResources(
                in: $resource['path'],
                for: $resource['namespace'],
            );
        }

        foreach (ModuleManager::getClusters() as $cluster) {
            $panel->discoverClusters(
                in: $cluster['path'],
                for: $cluster['namespace'],
            );
        }

        return $panel
            ->path(app('panelis')['path'] ?? '')
            ->domain(app('panelis')['domain'] ?? '')

            ->brandLogo(function (): ?string {
                if (filled(config('app.logo')) && config('app.use_logo_in_panel')) {
                    return Storage::url(config('app.logo'));
                }

                return null;
            })
            ->favicon(function (): ?string {
                if (filled(config('app.favicon'))) {
                    return Storage::url(config('app.favicon'));
                }

                return null;
            })

            ->plugins([
                // TodoPlugin::make(),
            ])
            ->databaseNotifications()
            ->navigationGroups([
                NavigationGroup::make(__('location.label'))
                    ->icon(Heroicon::OutlinedMapPin)
                    ->collapsed(),

                NavigationGroup::make(__('user.label'))
                    ->collapsed()
                    ->icon(Heroicon::OutlinedUserGroup),

                NavigationGroup::make(__('job.label'))
                    ->collapsed()
                    ->icon(Heroicon::OutlinedCalendar),

                NavigationGroup::make(__('ui.system'))
                    ->collapsed()
                    ->icon(Heroicon::OutlinedCog8Tooth),
            ])

            // ->registration(Register::class)
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
                AccountWidget::class,
                FilamentInfoWidget::class,
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
                RegisterNavigations::class,
                SetTheme::class,
            ])
            ->authMiddleware([
                Authenticate::class,

                // custom middlewares
            ]);
    }
}
