<?php

namespace App\Providers\Filament;

use App\Filament\Pages\GeneralReports;
use App\Filament\Widgets\DashboardStats;
use App\Filament\Widgets\FinancialTrendsChart;
use App\Filament\Widgets\ProjectsByStatusChart;
use App\Filament\Widgets\RecentProjectsTable;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->brandLogo(asset('images/logo.png'))
            ->brandLogoHeight('6rem')
            ->darkMode(false)
            ->colors([
                'primary' => '#ebb436',
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
                ProjectsByStatusChart::class,
                FinancialTrendsChart::class,
                RecentProjectsTable::class,
            ])
            ->databaseTransactions()
            ->spa();
        ;
    }
}
