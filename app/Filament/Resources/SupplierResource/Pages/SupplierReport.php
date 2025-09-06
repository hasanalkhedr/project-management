<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use App\Models\Currency;
use App\Models\Expense;
use App\Models\Supplier;
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

class SupplierReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = SupplierResource::class;
    protected static string $view = 'filament.resources.supplier-resource.pages.supplier-report';

    public array $data = [];

    public Supplier $record;
    public function getBreadcrumb(): ?string
    {
        return __('Supplier Report');
    }

    public function getHeading(): string
    {
        return __('Supplier Report');
    }

    public function mount(Supplier $record): void
    {
        $this->record = $record;
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
                    ->from('expenses')
                    ->where('supplier_id', $this->record->id);
            })
            ->pluck('name', 'id')
            ->prepend(__('All Currencies'), 'all');

        $projectOptions = Project::query()
            ->whereIn('id', function ($query) {
                $query->select('project_id')
                    ->from('expenses')
                    ->where('supplier_id', $this->record->id);
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
        return Expense::query()
            ->where('supplier_id', $this->record->id)
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
        $query = Expense::query()
            ->where('supplier_id', $this->record->id)
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

        // Get all suppliers once (for both summaries)
        $expenses = $query->get();

        // Group by currency for currency summary
        $expensesByCurrency = $expenses->groupBy('currency.code');

        // Group by project for project summary (already currency-filtered)
        $expensesByProject = $expenses->groupBy(['project.name']);

        // Calculate totals for each currency
        $currencySummaries = [];
        foreach ($expensesByCurrency as $currencyCode => $expenses) {
            $sumSuppliers = $expenses->sum('amount');
            $currencySummaries[$currencyCode] = $sumSuppliers;
        }
        // Calculate totals for each project (with currency filter applied)
        $projectSummaries = [];
        foreach ($expensesByProject as $projectName => $expenses) {
            $projectSummaries[$projectName] = [];
            $sumSuppliers = $expenses->groupBy('currency.code');
            foreach ($sumSuppliers as $currencyCode => $expenses) {
                $projectSummaries[$projectName][$currencyCode] = $expenses->sum('amount');
            }
        }
        return [
            'by_currency' => $currencySummaries,
            'by_project' => $projectSummaries,
        ];
    }
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

        $filename = "كشف حساب المورّد ".$this->record->name.' ';
        $filename .= ($selectedProject ? "للمشروع {$selectedProject->name} - " : " ");
        $filename .= ($selectedCurrency ? "بال{$selectedCurrency->name} - " : "بكل العملات ");
        $report_title = $filename;
        $filename .= '.pdf';

        return new StreamedResponse(function () use ($selectedCurrency, $selectedProject, $report_title) {
            $summary = $this->getSummary();
            $expenses = $this->getTableQuery()->get();

            $data = [
                'start_date' => $this->data['start_date'] ?? null,
                'end_date' => $this->data['end_date'] ?? null,
                'currency_filter' => $selectedCurrency ? $selectedCurrency->name : __('All Currencies'),
                'project_filter' => $selectedProject ? $selectedProject : __('All Projects'),
                'by_currency' => $summary['by_currency'],
                'by_project' => $summary['by_project'], // Add project summary data
                'expenses' => $expenses,
                'logo' => 'file://' . public_path('images/logo.png'),
                'company_name' => 'file://' . public_path('images/name.png'),
                'report_date' => now()->translatedFormat('j F Y'),
                'report_title' => $report_title,
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
            $html = view('filament.resources.supplier-resource.pages.supplier-report-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
