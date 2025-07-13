<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FinancialTrendsChart extends ChartWidget
{
    protected static ?string $heading = 'Financial Trends (Last 30 Days)';

    protected function getData(): array
    {
        $expenses = $this->getDailyData('expenses');
        $payments = $this->getDailyData('payments');

        return [
            'labels' => array_keys($expenses),
            'datasets' => [
                [
                    'label' => 'Expenses',
                    'data' => array_values($expenses),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef444480',
                ],
                [
                    'label' => 'Payments',
                    'data' => array_values($payments),
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b98180',
                ],
            ],
        ];
    }

    protected function getDailyData(string $model): array
    {
        $table = $model === 'expenses' ? 'expenses' : 'payments';

        $data = DB::table($table)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%b %d') as date"),
                DB::raw("COALESCE(SUM(amount), 0) as total")
            )
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('created_at')
            ->orderBy('created_at')
            ->pluck('total', 'date')
            ->toArray();

        // Fill in missing dates with 0
        $fullRange = [];
        for ($i = 30; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('M d');
            $fullRange[$date] = $data[$date] ?? 0;
        }

        return $fullRange;
    }

    protected function getType(): string
    {
        return 'line';
    }
}
