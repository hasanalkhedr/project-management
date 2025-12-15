<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Currency;
use App\Models\Transaction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Number;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TransactionReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = TransactionResource::class;
    protected static string $view = 'filament.resources.transaction-resource.pages.transaction-report';

    public array $data = [];

    public function getBreadcrumb(): ?string
    {
        return __('Transaction Report');
    }

    public function getHeading(): string
    {
        return __('Transaction Report');
    }

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->subYears(5),
            'end_date' => now()->addyears(5),
        ]);
    }

    public function form(Form $form): Form
    {
        $currencyOptions = Currency::query()
            ->whereIn('id', function ($query) {
                $query->select('currency_id')
                    ->from('transactions');
            })
            ->pluck('name', 'id')
            ->prepend(__('All Currencies'), 'all');

        return $form
            ->schema([
                Select::make('currency_id')
                    ->label(__('Select Currency'))
                    ->options($currencyOptions)
                    ->required()
                    ->default('all'),

                Select::make('report_type')
                    ->label(__('Report Type'))
                    ->options([
                        'both' => __('Both Payments and Expenses'),
                        'payment' => __('Payments Only'),
                        'expense' => __('Expenses Only'),
                    ])
                    ->required()
                    ->default('both'),

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
        return Transaction::query()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->when(
                ($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all',
                fn($q) => $q->where('currency_id', $this->data['currency_id'])
            )
            ->when(
                ($this->data['report_type'] ?? 'both') !== 'both',
                fn($q) => $q->where('type', $this->data['report_type'])
            )
            ->with('currency')
            ->orderBy('date', 'desc');
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('date')
                ->translateLabel()
                ->date(),

            Tables\Columns\TextColumn::make('description')
                ->translateLabel()
                ->searchable(),

            Tables\Columns\BadgeColumn::make('type')
                    ->translateLabel()
                    ->colors([
                        'danger' => 'expense',
                        'success' => 'payment',
                    ])
                    ->formatStateUsing(fn(string $state): string => __(ucfirst($state)))
                    ->sortable(),
            Tables\Columns\TextColumn::make('amount')
                ->numeric(decimalPlaces: 2)
                ->translateLabel()
                ->color(fn($record) => $record->type === 'expense' ? 'danger' : 'success'),

            Tables\Columns\TextColumn::make('currency.code')
                ->translateLabel()
                ->label('Currency'),

            Tables\Columns\TextColumn::make('payment_method')
                ->translateLabel()
                ->default('-'),

            Tables\Columns\TextColumn::make('reference')
                ->translateLabel()
                ->default('-'),
        ];
    }

    public function generateReport(): void
    {
        $this->resetTable();
    }

    public function getSummary(): array
    {
        $reportType = $this->data['report_type'] ?? 'both';

        // Get transactions grouped by currency
        $transactionsByCurrency = Transaction::query()
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

        foreach ($transactionsByCurrency as $currencyCode => $transactions) {
            $expenses = $transactions->where('type', 'expense')->sum('amount');
            $payments = $transactions->where('type', 'payment')->sum('amount');

            $currencySummaries[$currencyCode] = [
                'expenses' => $expenses,
                'payments' => $payments,
                'profit' => $payments - $expenses
            ];
        }

        return [
            'by_currency' => $currencySummaries,
            'total_expenses' => $transactionsByCurrency->flatten()->where('type', 'expense')->sum('amount'),
            'total_payments' => $transactionsByCurrency->flatten()->where('type', 'payment')->sum('amount'),
            'total_profit' => $transactionsByCurrency->flatten()->where('type', 'payment')->sum('amount') -
                             $transactionsByCurrency->flatten()->where('type', 'expense')->sum('amount'),
            'report_type' => $reportType
        ];
    }

    public function exportToPdf(): StreamedResponse
    {
        $selectedCurrency = null;
        if (($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all') {
            $selectedCurrency = Currency::find($this->data['currency_id']);
        }

        $reportType = $this->data['report_type'] ?? 'both';

        $filename = "كشف حساب " ;
        switch ($reportType) {
            case 'both':
                $filename .= "النفقات والدفعات الخاصة ";
                break;
            case 'payment':
                $filename .= "الدفعات الخاصة ";
                break;
            case 'expense':
                $filename .= "النفقات الخاصة ";
                break;
        }
         $filename .= ($selectedCurrency ? "بال{$selectedCurrency->name} - ":
            "بكل العملات - ") .' - ' . now()->format('Y-m-d') . '.pdf';

        return new StreamedResponse(function () use ($selectedCurrency, $reportType) {
            $summary = $this->getSummary();
            $transactions = $this->getTableQuery()->get();
            $data = [
                'start_date' => $this->data['start_date'] ?? null,
                'end_date' => $this->data['end_date'] ?? null,
                'currency_filter' => $selectedCurrency ? $selectedCurrency->name : __('All Currencies'),
                'report_type' => $reportType,
                'by_currency' => $summary['by_currency'],
                'total_expenses' => $summary['total_expenses'],
                'total_payments' => $summary['total_payments'],
                'total_profit' => $summary['total_profit'],
                'transactions' => $transactions,
                'logo' => 'file://' . public_path('images/alrayan-logo2025.png'),
                'stamp' => 'file://' . public_path('images/stamp.png'),
                'company_name' => 'file://' . public_path('images/name.png'),
                'report_date' => now()->translatedFormat('j F Y'),
            ];

            $defaultConfig = (new ConfigVariables())->getDefaults();
            $fontDirs = $defaultConfig['fontDir'];

            $defaultFontConfig = (new FontVariables())->getDefaults();
            $fontData = $defaultFontConfig['fontdata'];

            $mpdf = new Mpdf([
                'mode' => 'utf-8',
                'format' => 'A4',
                'direction' => 'rtl',
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
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ]
            ],
            'default_font' => 'xbriyaz',
                'margin_top' => 5,
            'margin_header' => 5,
            'margin_bottom' => 15,
            'margin_footer' => 5,
            'margin_left' => 8,
            'margin_right' => 8,
            'tempDir' => storage_path('app/mpdf/tmp'),
            'allow_output_buffering' => true,
            ]);
// Set footer with page number on left
        $mpdf->SetHTMLFooter('
            <div style="text-align: left; font-size: 10px; width: 100%;">
                الصفحة {PAGENO} من {nbpg}
            </div>
        ');
            $html = view('filament.resources.transaction-resource.pages.transaction-report-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
