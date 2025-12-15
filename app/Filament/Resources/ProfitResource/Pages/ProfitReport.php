<?php

namespace App\Filament\Resources\ProfitResource\Pages;

use App\Filament\Resources\ProfitResource;
use App\Models\Currency;
use App\Models\Profit;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProfitReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = ProfitResource::class;
    protected static string $view = 'filament.resources.profit-resource.pages.profit-report';

    public array $data = [];

    public function getBreadcrumb(): ?string
    {
        return __('Profit Report');
    }

    public function getHeading(): string
    {
        return __('Profit Report');
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
                    ->from('profits');
            })
            ->pluck('name', 'id')
            ->prepend(__('All Currencies'), 'all');

        $projectOptions = Project::query()
            ->whereIn('id', function ($query) {
                $query->select('project_id')
                    ->from('profits');
            })
            ->pluck('name', 'id')
            ->prepend(__('All Projects'), 'all');

        return $form
            ->schema([
                Select::make('currency_id')
                    ->label(__('Select Currency'))
                    ->options($currencyOptions)
                    ->required()
                    ->default('all'),

                Select::make('project_id')
                    ->label(__('Select Project'))
                    ->options($projectOptions)
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
        return Profit::query()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->when(
                ($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all',
                fn($q) => $q->where('currency_id', $this->data['currency_id'])
            )
            ->when(
                ($this->data['project_id'] ?? null) && $this->data['project_id'] !== 'all',
                fn($q) => $q->where('project_id', $this->data['project_id'])
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

            Tables\Columns\TextColumn::make('project.name')
                ->translateLabel()
                ->label('Project'),

            Tables\Columns\TextColumn::make('description')
                ->translateLabel()
                ->searchable(),

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
        $query = Profit::query()
            ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
            ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
            ->when(
                ($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all',
                fn($q) => $q->where('currency_id', $this->data['currency_id'])
            )
            ->when(
                ($this->data['project_id'] ?? null) && $this->data['project_id'] !== 'all',
                fn($q) => $q->where('project_id', $this->data['project_id'])
            )
            ->with(['currency', 'project']);

        // Get all profits once (for both summaries)
        $profits = $query->get();

        // Group by currency for currency summary
        $profitsByCurrency = $profits->groupBy('currency.code');

        // Group by project for project summary (already currency-filtered)
        $profitsByProject = $profits->groupBy('project.name');

        // Calculate totals for each currency
        $currencySummaries = [];
        foreach ($profitsByCurrency as $currencyCode => $profits) {
            $sumProfits = $profits->sum('amount');
            $currencySummaries[$currencyCode] = $sumProfits;
        }

        // Calculate totals for each project (with currency filter applied)
        $projectSummaries = [];
        foreach ($profitsByProject as $projectName => $profits) {
            $projectSummaries[$projectName] = [];
            $sumProfits = $profits->groupBy('currency.code');
            foreach ($sumProfits as $currencyCode => $profits) {
                $projectSummaries[$projectName][$currencyCode] = $profits->sum('amount');
            }
        }

        return [
            'by_currency' => $currencySummaries,
            'by_project' => $projectSummaries,
        ];
    }
    // public function getSummary(): array
    // {
    //     // Get profits grouped by currency
    //     $profitsByCurrency = Profit::query()
    //         ->when($this->data['start_date'] ?? null, fn($q, $date) => $q->where('date', '>=', $date))
    //         ->when($this->data['end_date'] ?? null, fn($q, $date) => $q->where('date', '<=', $date))
    //         ->when(
    //             ($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all',
    //             fn($q) => $q->where('currency_id', $this->data['currency_id'])
    //         )
    //         ->when(
    //             ($this->data['project_id'] ?? null) && $this->data['project_id'] !== 'all',
    //             fn($q) => $q->where('project_id', $this->data['project_id'])
    //         )
    //         ->with('currency')
    //         ->get()
    //         ->groupBy('currency.code');

    //     // Calculate totals for each currency
    //     $currencySummaries = [];



    //     foreach ($profitsByCurrency as $currencyCode => $profits) {
    //         $sumProfits = $profits->sum('amount');
    //         $currencySummaries[$currencyCode] = $sumProfits;
    //     }
    //     return [
    //         'by_currency' => $currencySummaries,
    //     ];

    // }

    public function exportToPdf(): StreamedResponse
    {
        $this->validate([
            'data.currency_id' => 'required',
            'data.project_id' => 'required',
        ]);
        $selectedCurrency = null;
        if (($this->data['currency_id'] ?? null) && $this->data['currency_id'] !== 'all') {
            $selectedCurrency = Currency::find($this->data['currency_id']);
        }

        $selectedProject = null;
        if (($this->data['project_id'] ?? null) && $this->data['project_id'] !== 'all') {
            $selectedProject = Project::find($this->data['project_id']); // Fixed: Changed from Currency to Project
        }

        $filename = "كشف حساب دفعات الإشراف ";
        $filename .= ($selectedProject ? "{$selectedProject->name} - " : "كل المشاريع - ");
        $filename .= ($selectedCurrency ? "بال{$selectedCurrency->name} - " : "بكل العملات - ") . now()->format('Y-m-d') . '.pdf';

        return new StreamedResponse(function () use ($selectedCurrency, $selectedProject) {
            $summary = $this->getSummary();
            $profits = $this->getTableQuery()->get();

            $data = [
                'start_date' => $this->data['start_date'] ?? null,
                'end_date' => $this->data['end_date'] ?? null,
                'currency_filter' => $selectedCurrency ? $selectedCurrency->name : __('All Currencies'),
                'project_filter' => $selectedProject ? $selectedProject : __('All Projects'),
                'by_currency' => $summary['by_currency'],
                'by_project' => $summary['by_project'], // Add project summary data
                'profits' => $profits,
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
            $html = view('filament.resources.profit-resource.pages.profit-report-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
