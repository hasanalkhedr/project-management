<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;

class GeneralReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static string $view = 'filament.pages.general-reports';
    protected static ?int $navigationSort = 6;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('start_date')
                    ->label('من تاريخ'),
                DatePicker::make('end_date')
                    ->label('إلى تاريخ'),
            ])
            ->statePath('data');
    }

    public function getReports()
    {
        $projects = Project::with(['expenses.currency', 'payments.currency'])
            ->get();

        $expensesByCurrency = collect();
        $paymentsByCurrency = collect();

        foreach ($projects as $project) {
            $projectExpenses = $project->expenses
                ->when($this->data['start_date'], fn($query, $date) => $query->where('date', '>=', $date))
                ->when($this->data['end_date'], fn($query, $date) => $query->where('date', '<=', $date));

            $projectPayments = $project->payments
                ->when($this->data['start_date'], fn($query, $date) => $query->where('date', '>=', $date))
                ->when($this->data['end_date'], fn($query, $date) => $query->where('date', '<=', $date));

            foreach ($projectExpenses->groupBy('currency.code') as $currency => $expenses) {
                $expensesByCurrency[$currency] = ($expensesByCurrency[$currency] ?? 0) + $expenses->sum('amount');
            }

            foreach ($projectPayments->groupBy('currency.code') as $currency => $payments) {
                $paymentsByCurrency[$currency] = ($paymentsByCurrency[$currency] ?? 0) + $payments->sum('amount');
            }
        }

        return [
            'projects' => $projects,
            'expensesByCurrency' => $expensesByCurrency,
            'paymentsByCurrency' => $paymentsByCurrency,
        ];
    }
}
