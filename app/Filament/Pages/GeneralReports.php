<?php

namespace App\Filament\Pages;

use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Actions\Action;
use Barryvdh\DomPDF\Facade\Pdf;
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
            'start_date' => now()->subMonth(),
            'end_date' => now(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('start_date')
                    ->label(__('general.start_date'))
                    ->required(),
                DatePicker::make('end_date')
                    ->label(__('general.end_date'))
                    ->required(),
            ])
            ->columns(2)
            ->statePath('data');
    }

    public function getReports(): array
    {
        $projects = Project::with(['expenses.currency', 'payments.currency'])
            ->get();

        $currencySummaries = collect();
        $projectSummaries = collect();

        foreach ($projects as $project) {
            $projectExpenses = $project->expenses
                ->when($this->data['start_date'] ?? null, fn($query, $date) => $query->where('date', '>=', $date))
                ->when($this->data['end_date'] ?? null, fn($query, $date) => $query->where('date', '<=', $date));

            $projectPayments = $project->payments
                ->when($this->data['start_date'] ?? null, fn($query, $date) => $query->where('date', '>=', $date))
                ->when($this->data['end_date'] ?? null, fn($query, $date) => $query->where('date', '<=', $date));

            // Group expenses and payments by currency for the project
            $projectExpenses->groupBy('currency.code')->each(function ($expenses, $currency) use (&$currencySummaries) {
                $currencySummaries->put($currency, [
                    'expenses' => ($currencySummaries->get($currency)['expenses'] ?? 0) + $expenses->sum('amount'),
                    'payments' => ($currencySummaries->get($currency)['payments'] ?? 0),
                    'profit' => ($currencySummaries->get($currency)['profit'] ?? 0) - $expenses->sum('amount')
                ]);
            });

            $projectPayments->groupBy('currency.code')->each(function ($payments, $currency) use (&$currencySummaries) {
                $currencySummaries->put($currency, [
                    'expenses' => ($currencySummaries->get($currency)['expenses'] ?? 0),
                    'payments' => ($currencySummaries->get($currency)['payments'] ?? 0) + $payments->sum('amount'),
                    'profit' => ($currencySummaries->get($currency)['profit'] ?? 0) + $payments->sum('amount')
                ]);
            });

            // Calculate project summary
            $projectSummary = [
                'name' => $project->name,
                'currencies' => []
            ];

            // Get all currencies used in this project
            $currencies = $projectExpenses->pluck('currency.code')
                ->merge($projectPayments->pluck('currency.code'))
                ->unique()
                ->filter();

            foreach ($currencies as $currency) {
                $expenses = $projectExpenses->where('currency.code', $currency)->sum('amount');
                $payments = $projectPayments->where('currency.code', $currency)->sum('amount');
                $profit = $payments - $expenses;

                $projectSummary['currencies'][$currency] = [
                    'expenses' => $expenses,
                    'payments' => $payments,
                    'profit' => $profit
                ];
            }

            $projectSummaries->push($projectSummary);
        }

        return [
            'currencySummaries' => $currencySummaries,
            'projectSummaries' => $projectSummaries,
        ];
    }
    public function exportToPdf(): StreamedResponse
    {
        return new StreamedResponse(function () {
            $reports = $this->getReports();

            $data = [
                'currencySummaries' => $reports['currencySummaries']->toArray(),
                'projectSummaries' => $reports['projectSummaries']->toArray(),
                'start_date' => $this->data['start_date'] ?? null,
                'end_date' => $this->data['end_date'] ?? null,
                'logo' => 'file://' . public_path('images/logo.png'),
                'report_date' => now()->translatedFormat('j F Y'),
                'report_title' => 'تقرير عام لجميع المشاريع',
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
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_left' => 10,
                'margin_right' => 10,
                'tempDir' => storage_path('app/mpdf/tmp'),
                'allow_output_buffering' => true,
            ]);

            $html = view('filament.pages.general-reports-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="تقرير عام - ' . now()->format('Y-m-d') . '.pdf"',
        ]);
    }
}
