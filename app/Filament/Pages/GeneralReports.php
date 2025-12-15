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
                    'supplier' => $expense->supplier,
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
    // public function getReports(): array
// {
//     $projects = Project::with(['expenses.currency', 'payments.currency', 'client'])->get();

    //     $currencySummaries = collect();
//     $projectSummaries = collect();
//     $allTransactions = collect();

    //     foreach ($projects as $project) {
//         // Filter expenses
//         $projectExpenses = $project->expenses
//             ->when($this->data['start_date'] ?? null, fn($query, $date) => $query->where('date', '>=', $date))
//             ->when($this->data['end_date'] ?? null, fn($query, $date) => $query->where('date', '<=', $date))
//             ->when($this->data['currency_id'] !== 'all', fn($query) => $query->where('currency_id', $this->data['currency_id']));

    //         // Filter payments
//         $projectPayments = $project->payments
//             ->when($this->data['start_date'] ?? null, fn($query, $date) => $query->where('date', '>=', $date))
//             ->when($this->data['end_date'] ?? null, fn($query, $date) => $query->where('date', '<=', $date))
//             ->when($this->data['currency_id'] !== 'all', fn($query) => $query->where('currency_id', $this->data['currency_id']));

    //         // Handle report type
//         if ($this->data['report_type'] === 'payments') {
//             $projectExpenses = collect([]);
//         } elseif ($this->data['report_type'] === 'expenses') {
//             $projectPayments = collect([]);
//         }

    //         // Prepare transactions for the combined table
//         $projectExpenses->each(function ($expense) use ($project, &$allTransactions) {
//             $allTransactions->push([
//                 'date' => $expense->date,
//                 'type' => __('Expense'),
//                 'project' => $project->name,
//                 'description' => $expense->description,
//                 'supplier' => $expense->supplier,
//                 'invoice_number' => $expense->invoice_number,
//                 'amount' => $expense->amount,
//                 'currency' => $expense->currency->code,
//                 'timestamp' => strtotime($expense->date),
//             ]);
//         });

    //         $projectPayments->each(function ($payment) use ($project, &$allTransactions) {
//             $allTransactions->push([
//                 'date' => $payment->date,
//                 'type' => __('Payment'),
//                 'project' => $project->name,
//                 'description' => $payment->description,
//                 'supplier' => $payment->payment_method,
//                 'invoice_number' => $payment->reference,
//                 'amount' => $payment->amount,
//                 'currency' => $payment->currency->code,
//                 'timestamp' => strtotime($payment->date),
//             ]);
//         });

    //         // Group by currency for summary
//         $projectSummary = [
//             'name' => $project->name,
//             'client' => $project->client,
//             'currencies' => [],
//         ];

    //         // Get all currencies used in this project
//         $currencies = $projectExpenses->pluck('currency.code')
//             ->merge($projectPayments->pluck('currency.code'))
//             ->unique()
//             ->filter();

    //         foreach ($currencies as $currency) {
//             $expenses = $projectExpenses->where('currency.code', $currency)->sum('amount');
//             $payments = $projectPayments->where('currency.code', $currency)->sum('amount');
//             $profit = $payments - $expenses;

    //             $projectSummary['currencies'][$currency] = [
//                 'expenses' => $expenses,
//                 'payments' => $payments,
//                 'profit' => $profit
//             ];
//         }

    //         // Only add project summary if it has data for the selected filters
//         if (!empty($projectSummary['currencies'])) {
//             $projectSummaries->push($projectSummary);
//         }
//     }

    //     // Sort transactions by date (newest first)
//     $sortedTransactions = $allTransactions->sortByDesc('timestamp');

    //     return [
//         'currencySummaries' => $currencySummaries->toArray(),
//         'projectSummaries' => $projectSummaries->toArray(),
//         'transactions' => $sortedTransactions->values()->all(),
//     ];
// }
// public function getReports(): array
    // {
    //     $projects = Project::with(['expenses.currency', 'payments.currency'])->get();

    //     $currencySummaries = collect();
    //     $projectSummaries = collect();

    //     foreach ($projects as $project) {
    //         // Filter expenses
    //         $projectExpenses = $project->expenses
    //             ->when($this->data['start_date'] ?? null, fn($query, $date) => $query->where('date', '>=', $date))
    //             ->when($this->data['end_date'] ?? null, fn($query, $date) => $query->where('date', '<=', $date))
    //             ->when($this->data['currency_id'] !== 'all', fn($query) => $query->where('currency_id', $this->data['currency_id']));

    //         // Filter payments
    //         $projectPayments = $project->payments
    //             ->when($this->data['start_date'] ?? null, fn($query, $date) => $query->where('date', '>=', $date))
    //             ->when($this->data['end_date'] ?? null, fn($query, $date) => $query->where('date', '<=', $date))
    //             ->when($this->data['currency_id'] !== 'all', fn($query) => $query->where('currency_id', $this->data['currency_id']));

    //         // Handle report type
    //         if ($this->data['report_type'] === 'payments') {
    //             $projectExpenses = collect([]);
    //         } elseif ($this->data['report_type'] === 'expenses') {
    //             $projectPayments = collect([]);
    //         }

    //         // Prepare detailed data for view
    //         $expensesDetails = $projectExpenses->map(fn($expense) => [
    //             'date' => $expense->date,
    //             'description' => $expense->description,
    //             'amount' => $expense->amount,
    //             'currency_code' => $expense->currency->code,
    //         ])->toArray();

    //         $paymentsDetails = $projectPayments->map(fn($payment) => [
    //             'date' => $payment->date,
    //             'description' => $payment->description,
    //             'amount' => $payment->amount,
    //             'currency_code' => $payment->currency->code,
    //         ])->toArray();

    //         // Group by currency for summary
    //         $projectSummary = [
    //             'name' => $project->name,
    //             'currencies' => [],
    //             'expenses_details' => $expensesDetails,
    //             'payments_details' => $paymentsDetails,
    //         ];


    //         // Group expenses and payments by currency for the project
    //         $projectExpenses->groupBy('currency.code')->each(function ($expenses, $currency) use (&$currencySummaries) {
    //             $currencySummaries->put($currency, [
    //                 'expenses' => ($currencySummaries->get($currency)['expenses'] ?? 0) + $expenses->sum('amount'),
    //                 'payments' => ($currencySummaries->get($currency)['payments'] ?? 0),
    //                 'profit' => ($currencySummaries->get($currency)['profit'] ?? 0) - $expenses->sum('amount')
    //             ]);
    //         });

    //         $projectPayments->groupBy('currency.code')->each(function ($payments, $currency) use (&$currencySummaries) {
    //             $currencySummaries->put($currency, [
    //                 'expenses' => ($currencySummaries->get($currency)['expenses'] ?? 0),
    //                 'payments' => ($currencySummaries->get($currency)['payments'] ?? 0) + $payments->sum('amount'),
    //                 'profit' => ($currencySummaries->get($currency)['profit'] ?? 0) + $payments->sum('amount')
    //             ]);
    //         });


    //         // Get all currencies used in this project
    //         $currencies = $projectExpenses->pluck('currency.code')
    //             ->merge($projectPayments->pluck('currency.code'))
    //             ->unique()
    //             ->filter();

    //         foreach ($currencies as $currency) {
    //             $expenses = $projectExpenses->where('currency.code', $currency)->sum('amount');
    //             $payments = $projectPayments->where('currency.code', $currency)->sum('amount');
    //             $profit = $payments - $expenses;

    //             $projectSummary['currencies'][$currency] = [
    //                 'expenses' => $expenses,
    //                 'payments' => $payments,
    //                 'profit' => $profit
    //             ];
    //         }

    //         // Only add project summary if it has data for the selected filters
    //         if (!empty($projectSummary['currencies'])) {
    //             $projectSummaries->push($projectSummary);
    //         }
    //     }

    //     return [
    //         'currencySummaries' => $currencySummaries->toArray(),
    //         'projectSummaries' => $projectSummaries->toArray(),
    //     ];
    // }
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
                    'xbriyaz' => [
                        'R' => 'XB Riyaz.ttf',
                        'B' => 'XB RiyazBd.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ]
                ],
                'default_font' => 'xbriyaz',
                'margin_top' => 5,
                'margin_bottom' => 15,
                'margin_left' => 10,
                'margin_right' => 10,
                'tempDir' => storage_path('app/mpdf/tmp'),
                'allow_output_buffering' => true,
            ]);
            // Set footer with page number on left
            $mpdf->SetHTMLFooter('
            <div style="text-align: left; font-size: 10px; width: 100%;">
                الصفحة {PAGENO} من {nbpg}
            </div>
        ');
            $html = view('filament.pages.general-reports-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    // public function exportToPdf(): StreamedResponse
    // {
    //     return new StreamedResponse(function () {
    //         $reports = $this->getReports();

    //         $data = [
    //             'currencySummaries' => $reports['currencySummaries'],
    //             'projectSummaries' => $reports['projectSummaries'],
    //             'start_date' => $this->data['start_date'] ?? null,
    //             'end_date' => $this->data['end_date'] ?? null,
    //             'logo' => 'file://' . public_path('images/logo.png'),
    //             'report_date' => now()->translatedFormat('j F Y'),
    //             'report_title' => 'تقرير عام لجميع المشاريع',
    //         ];

    //         // Default font configuration
    //         $defaultConfig = (new ConfigVariables())->getDefaults();
    //         $fontDirs = $defaultConfig['fontDir'];

    //         $defaultFontConfig = (new FontVariables())->getDefaults();
    //         $fontData = $defaultFontConfig['fontdata'];

    //         $mpdf = new Mpdf([
    //             'mode' => 'utf-8',
    //             'format' => 'A4',
    //             'direction' => 'rtl',
    //             'autoScriptToLang' => true,
    //             'autoLangToFont' => true,
    //             'fontDir' => [
    //                 base_path('vendor/mpdf/mpdf/ttfonts'),
    //                 storage_path('fonts'),
    //             ],
    //             'fontdata' => [
    //                 'xbriyaz' => [
    //                     'R' => 'XB Riyaz.ttf',
    //                     'B' => 'XB RiyazBd.ttf',
    //                     'useOTL' => 0xFF,
    //                     'useKashida' => 75,
    //                 ]
    //             ],
    //             'default_font' => 'xbriyaz',
    //             'margin_top' => 15,
    //             'margin_bottom' => 15,
    //             'margin_left' => 10,
    //             'margin_right' => 10,
    //             'tempDir' => storage_path('app/mpdf/tmp'),
    //             'allow_output_buffering' => true,
    //         ]);

    //         $html = view('filament.pages.general-reports-pdf', $data)->render();
    //         $mpdf->WriteHTML($html);
    //         $mpdf->Output('', 'I');
    //     }, 200, [
    //         'Content-Type' => 'application/pdf',
    //         'Content-Disposition' => 'attachment; filename="تقرير عام - ' . now()->format('Y-m-d') . '.pdf"',
    //     ]);
    // }
}
