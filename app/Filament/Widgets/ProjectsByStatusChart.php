<?php

namespace App\Filament\Widgets;

use App\Models\Project;
use Filament\Widgets\ChartWidget;

class ProjectsByStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Projects by Status';
    protected static ?string $pollingInterval = '60s';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $statusCounts = Project::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return [
            'labels' => array_map(fn($status) => ucfirst(str_replace('_', ' ', $status)), array_keys($statusCounts)),
            'datasets' => [[
                'label' => 'Projects',
                'data' => array_values($statusCounts),
                'backgroundColor' => [
                    '#f59e0b', // planned - amber
                    '#3b82f6', // in_progress - blue
                    '#10b981', // completed - emerald
                    '#ef4444', // on_hold - red
                ],
            ]],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
