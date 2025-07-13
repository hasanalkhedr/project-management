<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Expense;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class ProjectReports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.resources.project-resource.pages.project-reports';

    public Project $record;
    public array $data = [];

    public function mount(Project $record): void
    {
        $this->record = $record;
        $this->form->fill([
            'start_date' => $this->record->start_date,
            'end_date' => now(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('start_date')
                    ->label('From Date')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('To Date')
                    ->required(),
            ])
            ->columns(2)
            ->statePath('data');
    }

    protected function getTableQuery()
    {
        return Expense::query()
            ->where('project_id', $this->record->id)
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->with('currency');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('date')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('description')
                ->searchable(),
            Tables\Columns\TextColumn::make('amount')
                ->numeric(decimalPlaces: 2)
                ->sortable(),
            Tables\Columns\TextColumn::make('currency.code')
                ->label('Currency'),
            Tables\Columns\TextColumn::make('supplier')
                ->searchable(),
            Tables\Columns\TextColumn::make('invoice_number')
                ->label('Invoice #'),
        ];
    }

    protected function getTableDefaultSortColumn(): ?string
    {
        return 'date';
    }

    protected function getTableDefaultSortDirection(): ?string
    {
        return 'desc';
    }

    // public function paymentsTable(Table $table): Table
    // {
    //     return $table
    //         ->query(
    //             fn () => $this->record->payments()
    //                 ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
    //                 ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
    //                 ->with('currency')
    //         )
    //         ->columns([
    //             Tables\Columns\TextColumn::make('date')
    //                 ->date()
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('description')
    //                 ->searchable(),
    //             Tables\Columns\TextColumn::make('amount')
    //                 ->numeric(decimalPlaces: 2)
    //                 ->sortable(),
    //             Tables\Columns\TextColumn::make('currency.code')
    //                 ->label('Currency'),
    //             Tables\Columns\TextColumn::make('payment_method')
    //                 ->label('Method'),
    //             Tables\Columns\TextColumn::make('reference'),
    //         ])
    //         ->defaultSort('date', 'desc');
    // }

    public function getSummary(): array
    {
        $expenses = $this->record->expenses()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->sum('amount');

        $payments = $this->record->payments()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->sum('amount');

        return [
            'expenses' => $expenses,
            'payments' => $payments,
            'profit' => $payments - $expenses
        ];
    }

    public function exportToPdf()
    {
        $data = $this->getReportData();

        $pdf = Pdf::loadView('filament.resources.project-resource.pages.project-report-pdf', $data)
            ->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn() => print ($pdf->output()),
            "project-report-{$this->record->id}.pdf"
        );
    }
    protected function getReportData(): array
    {
        $expenses = $this->record->expenses()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->with('currency')
            ->get();

        $payments = $this->record->payments()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->with('currency')
            ->get();

        return [
            'project' => $this->record,
            'expenses' => $expenses,
            'payments' => $payments,
            'start_date' => $this->data['start_date'] ?? null,
            'end_date' => $this->data['end_date'] ?? null,
            'total_expenses' => $expenses->sum('amount'),
            'total_payments' => $payments->sum('amount'),
            'profit' => $payments->sum('amount') - $expenses->sum('amount')
        ];
    }
}
