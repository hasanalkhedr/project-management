<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Project;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
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
        $expenses = Expense::query()
            ->where('project_id', $this->record->id)
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->selectRaw("id, date, description, amount, currency_id, supplier, invoice_number, 'expense' as type");

        $payments = Payment::query()
            ->where('project_id', $this->record->id)
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->selectRaw("id, date, description, amount, currency_id, payment_method as supplier, reference as invoice_number, 'payment' as type");

        $combined = $expenses->unionAll($payments);

        return Expense::query()
            ->from(DB::raw("({$combined->toSql()}) as combined"))
            ->mergeBindings($combined->getQuery())
            ->orderBy('type', 'desc')
            ->with('currency');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('date')
                ->date(),
            //->sortable(),
            Tables\Columns\TextColumn::make('description')
                ->searchable(),
            Tables\Columns\TextColumn::make('type')
                ->label('Type')
                ->formatStateUsing(fn(string $state) => ucfirst($state))
                ->color(fn(string $state) => $state === 'expense' ? 'danger' : 'success'),
            Tables\Columns\TextColumn::make('amount')
                ->numeric(decimalPlaces: 2)
                //->sortable()
                ->color(fn(string $state, $record) => $record->type === 'expense' ? 'danger' : 'success'),
            Tables\Columns\TextColumn::make('currency.code')
                ->label('Currency'),
            Tables\Columns\TextColumn::make('supplier')
                ->searchable()
                ->label('Supplier/Method'),
            Tables\Columns\TextColumn::make('invoice_number')
                ->label('Reference #'),
        ];
    }
    // protected function getTableQuery()
    // {
    //     return Expense::query()
    //         ->where('project_id', $this->record->id)
    //         ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
    //         ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
    //         ->with('currency');
    // }

    // protected function getTableColumns(): array
    // {
    //     return [
    //         Tables\Columns\TextColumn::make('date')
    //             ->date()
    //             ->sortable(),
    //         Tables\Columns\TextColumn::make('description')
    //             ->searchable(),
    //         Tables\Columns\TextColumn::make('amount')
    //             ->numeric(decimalPlaces: 2)
    //             ->sortable(),
    //         Tables\Columns\TextColumn::make('currency.code')
    //             ->label('Currency'),
    //         Tables\Columns\TextColumn::make('supplier')
    //             ->searchable(),
    //         Tables\Columns\TextColumn::make('invoice_number')
    //             ->label('Invoice #'),
    //     ];
    // }

    protected function getTableDefaultSortColumn(): ?string
    {
        return 'date';
    }

    protected function getTableDefaultSortDirection(): ?string
    {
        return 'desc';
    }

    public function generateReport(): void
    {
        // This will trigger the table and summary to refresh
        $this->resetTable();
    }
    public function getSummary(): array
    {
        // Get expenses grouped by currency
        $expensesByCurrency = $this->record->expenses()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->with('currency')
            ->get()
            ->groupBy('currency.code');

        // Get payments grouped by currency
        $paymentsByCurrency = $this->record->payments()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->with('currency')
            ->get()
            ->groupBy('currency.code');

        // Calculate totals for each currency
        $currencySummaries = [];
        $allCurrencies = $expensesByCurrency->keys()->merge($paymentsByCurrency->keys())->unique();

        foreach ($allCurrencies as $currencyCode) {
            $expenses = $expensesByCurrency->get($currencyCode, collect());
            $payments = $paymentsByCurrency->get($currencyCode, collect());

            $currencySummaries[$currencyCode] = [
                'expenses' => $expenses->sum('amount'),
                'payments' => $payments->sum('amount'),
                'profit' => $payments->sum('amount') - $expenses->sum('amount')
            ];
        }

        return [
            'by_currency' => $currencySummaries,
            'total_expenses' => $expensesByCurrency->flatten()->sum('amount'),
            'total_payments' => $paymentsByCurrency->flatten()->sum('amount'),
            'total_profit' => $paymentsByCurrency->flatten()->sum('amount') - $expensesByCurrency->flatten()->sum('amount')
        ];
    }

    public function exportToPdf()
    {
        $summary = $this->getSummary();

        $data = [
            'project' => $this->record,
            'start_date' => $this->data['start_date'] ?? null,
            'end_date' => $this->data['end_date'] ?? null,
            'by_currency' => $summary['by_currency'],
            'total_expenses' => $summary['total_expenses'],
            'total_payments' => $summary['total_payments'],
            'total_profit' => $summary['total_profit'],
            'expenses' => $this->record->expenses()
                ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
                ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
                ->with('currency')
                ->orderBy('date', 'desc')
                ->get()
                ->groupBy('currency.code'),
            'payments' => $this->record->payments()
                ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
                ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
                ->with('currency')
                ->orderBy('date', 'desc')
                ->get()
                ->groupBy('currency.code'),
            'logo' => public_path('images/logo.png'), // Update this path to your logo
            'report_date' => now()->format('F j, Y'),
        ];

        $pdf = Pdf::loadView('filament.resources.project-resource.pages.project-report-pdf', $data)
            ->setPaper('a4', 'landscape')
            ->setOption('margin-top', '35mm'); // Add space for header

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
            ->get()
            ->groupBy('currency.code');

        $payments = $this->record->payments()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->with('currency')
            ->get()
            ->groupBy('currency.code');

        return [
            'project' => $this->record,
            'expenses_by_currency' => $expenses,
            'payments_by_currency' => $payments,
            'start_date' => $this->data['start_date'] ?? null,
            'end_date' => $this->data['end_date'] ?? null,
        ];
    }
}
