<?php

namespace App\Providers\Filament;

use App\Http\Middleware\EnsureUserIsVitahrAdmin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * The VitaHR admin panel, moved here from the vitahr repository. It manages
 * the HR data (employees, work locations, attendance, account requests) on
 * the shared vitahr database via the 'vitahr' DB connection and auth guard.
 */
class VitahrPanelProvider extends PanelProvider
{
    public function register(): void
    {
        parent::register();

        FilamentView::registerRenderHook(
            'panels::styles.after',
            fn(): string => new HtmlString('
            <style>
                /* 1. Fix the Sidebar Header container height */
                .fi-sidebar-header {
                    height: auto !important;
                    padding-top: 1.5rem !important;
                    padding-bottom: 1.5rem !important;
                }

                /* 2. Fix the Topbar Header height (if using top navigation) */
                @media (min-width: 1024px) {
                    .fi-topbar, .fi-topbar-nav {
                        height: 14rem !important; /* Slightly larger than your 12rem logo */
                    }
                }
            </style>
        '),
        );
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('vitahr')
            ->path('vitahr')
            ->authGuard('vitahr')
            ->brandLogo(asset('images/alr-logo.png'))
            ->brandLogoHeight('12rem')
            ->login()
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Vitahr/Resources'), for: 'App\\Filament\\Vitahr\\Resources')
            ->discoverPages(in: app_path('Filament/Vitahr/Pages'), for: 'App\\Filament\\Vitahr\\Pages')
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
            ])
            ->authMiddleware([
                Authenticate::class,
                EnsureUserIsVitahrAdmin::class,
            ]);
    }
}
