<?php

namespace App\Filament\Widgets;

use App\Models\Currency;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class FinancialTrendsChart extends ChartWidget
{
    //protected static ?string $heading = __('Financial Trends (Last 30 Days)');
public function getHeading(): string
    {
        return __('Financial Trends (Last 30 Days)');
    }
    protected static ?string $maxHeight = '400px';

    protected function getData(): array
    {
        $currencies = Currency::whereHas('expenses')
            ->orWhereHas('payments')
            ->get();

        $datasets = [];

        foreach ($currencies as $currency) {
            $expenses = $this->getDailyData('expenses', $currency);
            $payments = $this->getDailyData('payments', $currency);

            // Only add currency if it has data
            if (array_sum($expenses) > 0 || array_sum($payments) > 0) {
                $datasets[] = [
                    'currency' => $currency,
                    'expenses' => $expenses,
                    'payments' => $payments,
                ];
            }
        }

        if (empty($datasets)) {
            return [
                'labels' => [],
                'datasets' => [],
            ];
        }

        // If only one currency, show single chart
        if (count($datasets) === 1) {
            return $this->formatSingleCurrencyData($datasets[0]);
        }

        // For multiple currencies, show combined chart with different colors
        return $this->formatMultiCurrencyData($datasets);
    }

    protected function formatSingleCurrencyData(array $data): array
    {
        return [
            'labels' => array_keys($data['expenses']),
            'datasets' => [
                [
                    'label' => __('Expenses').' (' . $data['currency']->code . ')',
                    'data' => array_values($data['expenses']),
                    'borderColor' => '#ef4444',
                    'backgroundColor' => '#ef444480',
                    'tension' => 0.1,
                    'borderWidth' => 2,
                ],
                [
                    'label' => __('Payments').' (' . $data['currency']->code . ')',
                    'data' => array_values($data['payments']),
                    'borderColor' => '#10b981',
                    'backgroundColor' => '#10b98180',
                    'tension' => 0.1,
                    'borderWidth' => 2,
                ],
            ],
        ];
    }

    protected function formatMultiCurrencyData(array $datasets): array
    {
        $labels = array_keys($datasets[0]['expenses']);
        $chartData = ['labels' => $labels];

        $colorPalette = [
            '#3366CC', '#DC3912', '#FF9900', '#109618', '#990099',
            '#3B3EAC', '#0099C6', '#DD4477', '#66AA00', '#B82E2E',
            '#316395', '#994499', '#22AA99', '#AAAA11', '#6633CC',
            '#E67300', '#8B0707', '#329262', '#5574A6', '#3B3EAC'
        ];

        $lineStyles = [
            'expenses' => ['borderDash' => [5, 5], 'borderWidth' => 1.5],
            'payments' => ['borderWidth' => 2.5]
        ];

        foreach ($datasets as $index => $data) {
            $colorIndex = $index % count($colorPalette);
            $color = $colorPalette[$colorIndex];

            $chartData['datasets'][] = [
                'label' => __('Expenses').' (' . $data['currency']->code . ')',
                'data' => array_values($data['expenses']),
                'borderColor' => $this->adjustBrightness($color, -20),
                'backgroundColor' => $this->hexToRgba($color, 0.1),
                'tension' => 0.1,
                'pointBackgroundColor' => '#fff',
                'pointBorderColor' => $color,
                'pointHoverRadius' => 5,
            ] + $lineStyles['expenses'];

            $chartData['datasets'][] = [
                'label' => __('Payments').' (' . $data['currency']->code . ')',
                'data' => array_values($data['payments']),
                'borderColor' => $this->adjustBrightness($color, 20),
                'backgroundColor' => $this->hexToRgba($color, 0.1),
                'tension' => 0.1,
                'pointBackgroundColor' => '#fff',
                'pointBorderColor' => $color,
                'pointHoverRadius' => 5,
            ] + $lineStyles['payments'];
        }

        return $chartData;
    }

    protected function getDailyData(string $model, Currency $currency): array
    {
        $table = $model === 'expenses' ? 'expenses' : 'payments';

        $data = DB::table($table)
            ->select(
                DB::raw("DATE_FORMAT(date, '%b %d') as date_formatted"),
                DB::raw("COALESCE(SUM(amount), 0) as total")
            )
            ->where('currency_id', $currency->id)
            ->where('date', '>=', now()->subDays(30))
            ->groupBy('date_formatted')
            ->orderBy('date_formatted')
            ->pluck('total', 'date_formatted')
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

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                ],
            ],
            'interaction' => [
                'mode' => 'index',
                'intersect' => false,
            ],
        ];
    }

    protected function hexToRgba(string $hex, float $alpha): string
    {
        $hex = str_replace('#', '', $hex);
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgba($r, $g, $b, $alpha)";
    }

    protected function adjustBrightness(string $hex, int $steps): string
    {
        $steps = max(-255, min(255, $steps));
        $hex = str_replace('#', '', $hex);

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex($g), 2, '0', STR_PAD_LEFT)
            . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
    }
}
