<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use App\Models\Expense;
use App\Models\Payment;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            $this->getProjectsStat(),
            $this->getExpensesStat(),
            $this->getPaymentsStat(),
            $this->getProfitStat(),
        ];
    }

    protected function getProjectsStat(): Stat
    {
        $activeProjects = Project::whereIn('status', ['planned', 'in_progress'])->count();
        $completedProjects = Project::where('status', 'completed')->count();

        return Stat::make('Active Projects', $activeProjects)
            ->description($completedProjects . ' completed')
            ->descriptionIcon('heroicon-o-folder')
            ->chart([7, 2, 10, 3, 15, 4, 17])
            ->color('warning');
    }

    protected function getExpensesStat(): Stat
    {
        $totalExpenses = Expense::sum('amount');
        $trendData = $this->getExpenseTrendData();

        return Stat::make('Total Expenses', number_format($totalExpenses, 2))
            ->description('Last 30 days')
            ->descriptionIcon('heroicon-o-arrow-trending-down')
            ->chart($trendData)
            ->color('danger');
    }

    protected function getPaymentsStat(): Stat
    {
        $totalPayments = Payment::sum('amount');
        $trendData = $this->getPaymentTrendData();

        return Stat::make('Total Payments', number_format($totalPayments, 2))
            ->description('Last 30 days')
            ->descriptionIcon('heroicon-o-arrow-trending-up')
            ->chart($trendData)
            ->color('success');
    }

    protected function getProfitStat(): Stat
    {
        $totalExpenses = Expense::sum('amount');
        $totalPayments = Payment::sum('amount');
        $netProfit = $totalPayments - $totalExpenses;

        return Stat::make('Net Profit', number_format($netProfit, 2))
            ->description($netProfit >= 0 ? 'Profit' : 'Loss')
            ->descriptionIcon($netProfit >= 0 ? 'heroicon-o-banknotes' : 'heroicon-o-currency-dollar')
            ->color($netProfit >= 0 ? 'success' : 'danger');
    }

    protected function getExpenseTrendData(): array
    {
        return DB::table('expenses')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"),
                DB::raw("COALESCE(SUM(amount), 0) as total")
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('created_at')
            ->orderBy('date')
            ->pluck('total')
            ->toArray();
    }

    protected function getPaymentTrendData(): array
    {
        return DB::table('payments')
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') as date"),
                DB::raw("COALESCE(SUM(amount), 0) as total")
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('created_at')
            ->orderBy('date')
            ->pluck('total')
            ->toArray();
    }
}
