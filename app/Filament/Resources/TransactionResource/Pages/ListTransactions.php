<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Filament\Resources\TransactionResource\Widgets\TransStats;
use App\Filament\Widgets\TransactionStatsOverview;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ListRecords;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('report')
                ->label(__('View Report'))
                ->color('success')
                ->icon('heroicon-o-chart-bar')
                ->url($this->getResource()::getUrl('report')),
        ];
    }
    protected function getHeaderWidgets(): array
    {
        return [
            TransStats::class
        ];
    }
}
