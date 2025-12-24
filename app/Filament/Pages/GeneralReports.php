<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GeneralReports extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-chart-bar';
    protected static string $view = 'filament.pages.general-reports';
    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('general.reports');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('general.reports');
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'start_date' => now()->subyears(5),
            'end_date' => now()->addYears(5),
        ]);
    }

    public function form(Form $form): Form
    {
        $currencyOptions = \App\Models\Currency::query()
            ->whereIn('id', function ($query) {
                $query->select('currency_id')
                    ->from('expenses');
            })
            ->orWhereIn('id', function ($query) {
                $query->select('currency_id')
                    ->from('payments');
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
                        'payments' => __('Payments Only'),
                        'expenses' => __('Expenses Only'),
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
    // In App\Filament\Pages\GeneralReports.php
    public function getReports(): array
    {
        $projects = Project::with(['expenses.currency', 'payments.currency', 'client'])->get();

        $currencySummaries = [];
        $projectSummaries = [];
        $allTransactions = [];

        foreach ($projects as $project) {
            // Filter expenses
            $projectExpenses = $project->expenses
                ->when($this->data['start_date'] ?? null, fn($query, $date) => $query->where('date', '>=', $date))
                ->when($this->data['end_date'] ?? null, fn($query, $date) => $query->where('date', '<=', $date))
                ->when($this->data['currency_id'] !== 'all', fn($query) => $query->where('currency_id', $this->data['currency_id']));

            // Filter payments
            $projectPayments = $project->payments
                ->when($this->data['start_date'] ?? null, fn($query, $date) => $query->where('date', '>=', $date))
                ->when($this->data['end_date'] ?? null, fn($query, $date) => $query->where('date', '<=', $date))
                ->when($this->data['currency_id'] !== 'all', fn($query) => $query->where('currency_id', $this->data['currency_id']));

            // Handle report type
            if ($this->data['report_type'] === 'payments') {
                $projectExpenses = collect([]);
            } elseif ($this->data['report_type'] === 'expenses') {
                $projectPayments = collect([]);
            }

            // Prepare project summary
            $projectSummary = [
                'name' => $project->name,
                'client' => $project->client,
                'currencies' => [],
            ];

            // Group expenses and payments by currency for this project
            $expensesByCurrency = $projectExpenses->groupBy('currency.code');
            $paymentsByCurrency = $projectPayments->groupBy('currency.code');

            // Get all unique currencies for this project
            $currencies = $expensesByCurrency->keys()->merge($paymentsByCurrency->keys())->unique();

            foreach ($currencies as $currency) {
                $expenses = $expensesByCurrency->has($currency) ? $expensesByCurrency->get($currency)->sum('amount') : 0;
                $payments = $paymentsByCurrency->has($currency) ? $paymentsByCurrency->get($currency)->sum('amount') : 0;
                $profit = $payments - $expenses;

                // Add to project summary
                $projectSummary['currencies'][$currency] = [
                    'expenses' => $expenses,
                    'payments' => $payments,
                    'profit' => $profit
                ];

                // Initialize currency summary if not exists
                if (!isset($currencySummaries[$currency])) {
                    $currencySummaries[$currency] = [
                        'expenses' => 0,
                        'payments' => 0,
                        'profit' => 0
                    ];
                }

                // Accumulate to currency summaries
                $currencySummaries[$currency]['expenses'] += $expenses;
                $currencySummaries[$currency]['payments'] += $payments;
                $currencySummaries[$currency]['profit'] += $profit;
            }

            // Prepare transactions for the combined table
            foreach ($projectExpenses as $expense) {
                $allTransactions[] = [
                    'date' => $expense->date,
                    'type' => __('Expense'),
                    'project' => $project->name,
                    'description' => $expense->description,
                    'supplier' => $expense->supplier->name ?? '',
                    'invoice_number' => $expense->invoice_number,
                    'amount' => $expense->amount,
                    'currency' => $expense->currency->code,
                    'timestamp' => strtotime($expense->date),
                ];
            }

            foreach ($projectPayments as $payment) {
                $allTransactions[] = [
                    'date' => $payment->date,
                    'type' => __('Payment'),
                    'project' => $project->name,
                    'description' => $payment->description,
                    'supplier' => $payment->payment_method,
                    'invoice_number' => $payment->reference,
                    'amount' => $payment->amount,
                    'currency' => $payment->currency->code,
                    'timestamp' => strtotime($payment->date),
                ];
            }

            // Only add project summary if it has data for the selected filters
            if (!empty($projectSummary['currencies'])) {
                $projectSummaries[] = $projectSummary;
            }
        }

        // Sort transactions by date (newest first)
        usort($allTransactions, function ($a, $b) {
            return $b['timestamp'] <=> $a['timestamp'];
        });

        return [
            'currencySummaries' => $currencySummaries,
            'projectSummaries' => $projectSummaries,
            'transactions' => $allTransactions,
        ];
    }

    public function exportToPdf(): StreamedResponse
    {
        $selectedCurrency = null;
        if (($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all') {
            $selectedCurrency = \App\Models\Currency::find($this->data['currency_id']);
        }
        $filename = "كشف حساب ";
        switch ($this->data['report_type']) {
            case 'both':
                $filename .= "النفقات والدفعات ";
                break;
            case 'payments':
                $filename .= "الدفعات ";
                break;
            case 'expenses':
                $filename .= "النفقات ";
                break;
        }

        $filename .= ($selectedCurrency ? "بال{$selectedCurrency->name} - " :
            "بكل العملات - ") .
            'عام - ' . now()->format('Y-m-d') . '.pdf';
        return new StreamedResponse(function () use ($selectedCurrency) {
            $reports = $this->getReports();

            $data = [
                'currencySummaries' => $reports['currencySummaries'],
                'projectSummaries' => $reports['projectSummaries'],
                'transactions' => $reports['transactions'],
                'start_date' => $this->data['start_date'] ?? null,
                'end_date' => $this->data['end_date'] ?? null,
                'report_type' => $this->data['report_type'] ?? 'both',
                'logo' => 'file://' . public_path('images/alrayan-logo2025.png'),
                'stamp' => 'file://' . public_path('images/stamp.png'),
                'report_date' => now()->translatedFormat('j F Y'),
                'report_title' => 'تقرير عام لجميع المشاريع',
                'company_name' => 'file://' . public_path('images/name.png'),
                'currency_filter' => $selectedCurrency ? $selectedCurrency->name : __('All Currencies'),
            ];
            // Default font configuration
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
                    'almarai' => [
                        'R' => 'Almarai-Regular.ttf',
                        'B' => 'Almarai-ExtraBold.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ]
                ],
                'default_font' => 'almarai',
                'margin_top' => 5,
                'margin_bottom' => 40,
                'margin_left' => 4,
                'margin_right' => 4,
                'tempDir' => storage_path('app/mpdf/tmp'),
                'allow_output_buffering' => true,
            ]);
            // Set footer with page number on left
            $footerContent = '
    <div style="position: fixed; bottom: 0; left: 0; right: 0; text-align: center;">
        <img src="file://' . public_path('images/letterhead.png') . '" style="width: 100%; max-height: 338px; opacity: 1;" />
    </div>
';

$mpdf->SetHTMLFooter($footerContent);
            $html = view('filament.pages.general-reports-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
