<?php

namespace App\Providers\Filament;

use App\Filament\Pages\GeneralReports;
use App\Filament\Resources\TransactionResource\Widgets\TransStats;
use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\FinancialTrendsChart;
use App\Filament\Widgets\ProjectsByStatusChart;
use App\Filament\Widgets\RecentProjectsTable;
use BezhanSalleh\FilamentShield\FilamentShieldPlugin;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use TomatoPHP\FilamentUsers\FilamentUsersPlugin;

class AdminPanelProvider extends PanelProvider
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
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->passwordReset()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandLogo(asset('images/alr-logo.png'))
            ->brandName('شركة أبراج الريان للمقاولات')
            ->brandLogoHeight('12rem')
            ->font('Almarai')
            ->darkMode(false)
            ->colors([
                'primary' => '#1a6121',
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            //->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // ->navigationGroups([
            //     'إدارة المشاريع',
            //     'إدارة الماليات',
            //     'إدارة النظام',
            // ])
            // ->navigationItems([
            //     NavigationItem::make('التقارير العامة')
            //         ->url(fn(): string => GeneralReports::getUrl())
            //         ->icon('heroicon-o-chart-bar')
            //         ->group('إدارة الماليات')
            //         ->sort(1),
            // ])
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
            ])
            ->widgets([
                DashboardStats::class,
                TransStats::class,
                ProjectsByStatusChart::class,
                FinancialTrendsChart::class,
                RecentProjectsTable::class,
            ])
            ->plugin(FilamentUsersPlugin::make())
            ->plugin(FilamentShieldPlugin::make())
            ->databaseTransactions()
            ->spa();
        ;
    }
}
