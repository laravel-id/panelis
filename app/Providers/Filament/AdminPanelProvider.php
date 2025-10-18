<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\EmailVerificationPrompt;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\RequestPasswordReset;
use App\Filament\Pages\EditBranch;
use App\Filament\Pages\RegisterBranch;
use App\Http\Middleware\RegisterModules;
use App\Http\Middleware\RegisterNavigations;
use App\Http\Middleware\SetTheme;
use App\Models\Branch;
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
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->tenant(Branch::class, slugAttribute: 'slug')
            ->tenantRegistration(RegisterBranch::class)
            ->tenantProfile(EditBranch::class)
            ->default()
            ->id('admin')

            // uncomment to set different path
            ->path('admin')
            ->plugins([
                // TodoPlugin::make(),
            ])
            ->databaseNotifications()
            ->navigationGroups([
                NavigationGroup::make(__('blog.label'))
                    ->collapsed()
                    ->icon(Heroicon::OutlinedDocumentText),

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
