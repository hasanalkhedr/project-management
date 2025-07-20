<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\Project;
use App\Services\PdfService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DB;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectReports extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ProjectResource::class;
    protected static string $view = 'filament.resources.project-resource.pages.project-reports';

    public Project $record;
    public array $data = [];
    public function getBreadcrumb(): ?string
    {
        return __('Project Report');
    }
    public function getHeading(): string
    {
        return __('Project Report');
    }
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
        $currencyOptions = \App\Models\Currency::query()
        ->whereIn('id', function($query) {
            $query->select('currency_id')
                ->from('expenses')
                ->where('project_id', $this->record->id);
        })
        ->orWhereIn('id', function($query) {
            $query->select('currency_id')
                ->from('payments')
                ->where('project_id', $this->record->id);
        })
        ->pluck('name', 'id')
        ->prepend(__('All Currencies'), 'all');
        return $form
            ->schema([
                Select::make('currency_id')
                    ->label(__('Select Currency'))
                    ->options($currencyOptions)
                    ->columnSpanFull()
                    ->required()
                    ->default('all'),
                DatePicker::make('start_date')
                    ->label(__('From Date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label(__('To Date'))
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
            ->when(
                ($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all',
                fn($q) => $q->where('currency_id', $this->data['currency_id'])
            )
            ->selectRaw("id, date, description, amount, currency_id, supplier, invoice_number, '" . __('Expense') . "' as type");

        $payments = Payment::query()
            ->where('project_id', $this->record->id)
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->when(
                ($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all',
                fn($q) => $q->where('currency_id', $this->data['currency_id'])
            )
            ->selectRaw("id, date, description, amount, currency_id, payment_method as supplier, reference as invoice_number, '" . __('Payment') . "' as type");

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
                ->translateLabel()
                ->date(),
            //->sortable(),
            Tables\Columns\TextColumn::make('description')
                ->translateLabel()
                ->searchable(),
            Tables\Columns\TextColumn::make('type')
                ->translateLabel()
                ->label('Type')
                ->formatStateUsing(fn(string $state) => ucfirst($state))
                ->color(fn(string $state) => $state === __('Expense') ? 'danger' : 'success'),
            Tables\Columns\TextColumn::make('amount')
                ->numeric(decimalPlaces: 2)
                ->translateLabel()
                //->sortable()
                ->color(fn(string $state, $record) => $record->type === __('Expense') ? 'danger' : 'success'),
            Tables\Columns\TextColumn::make('currency.code')
                ->translateLabel()
                ->label('Currency'),
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
            ->when(
                ($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all',
                fn($q) => $q->where('currency_id', $this->data['currency_id'])
            )
            ->with('currency')
            ->get()
            ->groupBy('currency.code');

        // Get payments grouped by currency
        $paymentsByCurrency = $this->record->payments()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->when(
                ($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all',
                fn($q) => $q->where('currency_id', $this->data['currency_id'])
            )
            ->with('currency')
            ->get()
            ->groupBy('currency.code');

        // Calculate totals for each currency
        $currencySummaries = [];

        // Get all unique currency codes from both collections
        $allCurrencies = collect()
            ->merge($expensesByCurrency->keys())
            ->merge($paymentsByCurrency->keys())
            ->unique();

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

    public function exportToPdf(): StreamedResponse
    {
        // Get selected currency details if filtering
        $selectedCurrency = null;
        if (($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all') {
            $selectedCurrency = \App\Models\Currency::find($this->data['currency_id']);
        }

        $filename = ($selectedCurrency ?
            "كشف حساب (عملة {$selectedCurrency->code}) للمشروع - " :
            "كشف حساب (كل العملات) للمشروع - ") .
            $this->record->name . ' - ' . now()->format('Y-m-d') . '.pdf';

        return new StreamedResponse(function () use ($selectedCurrency) {
            $summary = $this->getSummary();

            $data = [
                'project' => $this->record,
                'start_date' => $this->data['start_date'] ?? null,
                'end_date' => $this->data['end_date'] ?? null,
                'currency_filter' => $selectedCurrency ? $selectedCurrency->name : __('All Currencies'),
                'by_currency' => $summary['by_currency'],
                'total_expenses' => $summary['total_expenses'],
                'total_payments' => $summary['total_payments'],
                'total_profit' => $summary['total_profit'],
                'transactions' => $this->getTableQuery()->get(),
                'logo' => 'file://' . public_path('images/logo.png'),
                'report_date' => now()->translatedFormat('j F Y'),
            ];

            // Default font configuration
            $defaultConfig = (new ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'direction' => 'rtl', // Essential for RTL
                'autoScriptToLang' => true,
                'autoLangToFont' => true,
                'fontDir' => [
                    base_path('vendor/mpdf/mpdf/ttfonts'),
                    storage_path('fonts'),
                ],
                'fontdata' => [
                    'xbriyaz' => [
                        'R' => 'XB Riyaz.ttf',
                        'B' => 'XB RiyazBd.ttf',
                        'useOTL' => 0xFF,  // Enable complex text layout
                        'useKashida' => 75, // Arabic justification
                    ]
                ],
                'default_font' => 'xbriyaz',
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_left' => 10,
                'margin_right' => 10,
                'tempDir' => storage_path('app/mpdf/tmp'),
                'allow_output_buffering' => true,
            ]);

            $html = view('filament.resources.project-resource.pages.project-report-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I'); // 'I' sends directly to output
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
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
