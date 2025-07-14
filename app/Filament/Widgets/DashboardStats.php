<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\Currency;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class DashboardStats extends BaseWidget
{
    protected function getStats(): array
    {
        $stats = [];

        // Get all active currencies that have expenses or payments
        $currencies = Currency::whereHas('expenses')
            ->orWhereHas('payments')
            ->get();

        // Add project stat (not currency-specific)
        //$stats[] = $this->getProjectsStat();

        foreach ($currencies as $currency) {
            $stats = array_merge($stats, [
                $this->getExpensesStat($currency),
                $this->getPaymentsStat($currency),
                $this->getProfitStat($currency),
            ]);
        }

        return $stats;
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

    protected function getExpensesStat(Currency $currency): Stat
    {
        $totalExpenses = Expense::where('currency_id', $currency->id)
            ->sum('amount');

        $trendData = $this->getExpenseTrendData($currency);

        return Stat::make("Total Expenses ({$currency->code})", number_format($totalExpenses, 2))
            ->description('Last 30 days')
            ->descriptionIcon('heroicon-o-arrow-trending-down')
            ->chart($trendData)
            ->color('danger');
    }

    protected function getPaymentsStat(Currency $currency): Stat
    {
        $totalPayments = Payment::where('currency_id', $currency->id)
            ->sum('amount');

        $trendData = $this->getPaymentTrendData($currency);

        return Stat::make("Total Payments ({$currency->code})", number_format($totalPayments, 2))
            ->description('Last 30 days')
            ->descriptionIcon('heroicon-o-arrow-trending-up')
            ->chart($trendData)
            ->color('success');
    }

    protected function getProfitStat(Currency $currency): Stat
    {
        $totalExpenses = Expense::where('currency_id', $currency->id)
            ->sum('amount');

        $totalPayments = Payment::where('currency_id', $currency->id)
            ->sum('amount');

        $netProfit = $totalPayments - $totalExpenses;

        return Stat::make("Net Profit ({$currency->code})", number_format($netProfit, 2))
            ->description($netProfit >= 0 ? 'Profit' : 'Loss')
            ->descriptionIcon($netProfit >= 0 ? 'heroicon-o-banknotes' : 'heroicon-o-currency-dollar')
            ->color($netProfit >= 0 ? 'success' : 'danger');
    }

    protected function getExpenseTrendData(Currency $currency): array
    {
        return Expense::select(
                DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as date_formatted"),
                DB::raw("COALESCE(SUM(amount), 0) as total")
            )
            ->where('currency_id', $currency->id)
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('date_formatted')
            ->orderBy('date_formatted')
            ->pluck('total')
            ->toArray();
    }

    protected function getPaymentTrendData(Currency $currency): array
    {
        return Payment::select(
                DB::raw("DATE_FORMAT(date, '%Y-%m-%d') as date_formatted"),
                DB::raw("COALESCE(SUM(amount), 0) as total")
            )
            ->where('currency_id', $currency->id)
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('date_formatted')
            ->orderBy('date_formatted')
            ->pluck('total')
            ->toArray();
    }
}
