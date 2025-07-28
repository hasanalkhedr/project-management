<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProject extends ViewRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\DeleteAction::make(),

            // // Add Expense Action
            // Actions\Action::make('addExpense')
            //     ->translateLabel()
            //     ->color('danger')
            //     ->icon('heroicon-o-banknotes')
            //     ->url(fn(): string => route('filament.admin.resources.expenses.create', [
            //         'project_id' => $this->record->id
            //     ])),

            // // Add Payment Action
            // Actions\Action::make('addPayment')
            //     ->translateLabel()
            //     ->color('primary')
            //     ->icon('heroicon-o-credit-card')
            //     ->url(fn(): string => route('filament.admin.resources.payments.create', [
            //         'project_id' => $this->record->id
            //     ])),

            // Reports Action
            Actions\Action::make('reports')
                ->translateLabel()
                ->color('success')
                ->icon('heroicon-o-chart-bar')
                ->url(fn(): string => ProjectReports::getUrl(['record' => $this->record])),
        ];
    }
}
