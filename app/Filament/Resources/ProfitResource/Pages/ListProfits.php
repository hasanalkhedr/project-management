<?php

namespace App\Filament\Resources\ProfitResource\Pages;

use App\Filament\Resources\ProfitResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProfits extends ListRecords
{
    protected static string $resource = ProfitResource::class;

     protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('report')
                ->label(__('View Profit Report'))
                ->color('success')
                ->icon('heroicon-o-chart-bar')
                ->url($this->getResource()::getUrl('report')),
        ];
    }
}
