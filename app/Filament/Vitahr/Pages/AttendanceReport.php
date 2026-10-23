<?php

namespace App\Filament\Vitahr\Pages;

use App\Models\Vitahr\AttendanceRecord;
use App\Models\Vitahr\Employee;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use App\Exports\AttendanceReportExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceReportPdfExport;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class AttendanceReport extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;
<<<<<<< HEAD
    protected static ?string $navigationGroup = 'نظام الحضور والتصراف';
=======
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0

    protected static ?string $navigationIcon =
        'heroicon-o-chart-bar';

    public static function getNavigationLabel(): string
    {
        return __('vitahr.attendance_report');
    }

    public function getTitle(): string
    {
        return __('vitahr.attendance_report');
    }

<<<<<<< HEAD
    protected static ?int $navigationSort = 7;
=======
    protected static ?int $navigationSort = 6;
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0

    protected static string $view =
        'filament.vitahr.pages.attendance-report';

    public array $employeeIds = [];

    public ?string $from = null;

    public ?string $until = null;

    public function mount(): void
    {
        $this->from = now()
            ->startOfMonth()
            ->toDateString();

        $this->until = now()
            ->toDateString();
    }

    protected function getEmployeeOptions(): array
    {
        return Employee::query()
            ->with('user')
            ->orderBy('employee_number')
            ->get()
            ->mapWithKeys(
                fn(Employee $employee): array => [
                    $employee->id =>
                        $employee->employee_number
                        . ' — '
                        . ($employee->user?->name ?? '-'),
                ]
            )
            ->all();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('employeeIds')
                    ->label(__('vitahr.employees'))
                    ->options(
                        fn(): array =>
                        $this->getEmployeeOptions()
                    )
                    ->multiple()
                    ->searchable()
                    ->preload()
                    ->placeholder(__('vitahr.all_employees'))
                    ->live(),

                Forms\Components\DatePicker::make('from')
                    ->label(__('vitahr.from'))
                    ->required()
                    ->live(),

                Forms\Components\DatePicker::make('until')
                    ->label(__('vitahr.until'))
                    ->required()
                    ->live(),
            ])
            ->columns(3);
    }

    protected function getBaseQuery(): Builder
    {
        return AttendanceRecord::query()
            ->with([
                'employee.user',
                'checkInWorkLocation',
                'checkInDevice',
                'checkOutDevice',
            ])
            ->when(
                !empty($this->employeeIds),
                fn(Builder $query) =>
                $query->whereIn(
                    'employee_id',
                    $this->employeeIds
                )
            )
            ->when(
                $this->from,
                fn(Builder $query, $date) =>
                $query->whereDate(
                    'check_in_at',
                    '>=',
                    $date
                )
            )
            ->when(
                $this->until,
                fn(Builder $query, $date) =>
                $query->whereDate(
                    'check_in_at',
                    '<=',
                    $date
                )
            );
    }

    protected function getRecordsQuery(): Builder
    {
        return $this->getBaseQuery()
            ->orderByDesc('check_in_at');
    }

    protected function getTotalRecords(): int
    {
        return $this->getBaseQuery()->count();
    }

    protected function getTotalCompleted(): int
    {
        return (clone $this->getBaseQuery())
            ->whereNotNull('check_out_at')
            ->count();
    }

    protected function getTotalOpen(): int
    {
        return (clone $this->getBaseQuery())
            ->whereNull('check_out_at')
            ->count();
    }

    protected function getTotalEmployees(): int
    {
        return (clone $this->getBaseQuery())
            ->distinct('employee_id')
            ->count('employee_id');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                fn(): Builder =>
                $this->getRecordsQuery()
            )
            ->columns([
                Tables\Columns\TextColumn::make(
                    'employee.employee_number'
                )
                    ->label(__('vitahr.employee_number'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'employee.user.name'
                )
                    ->label(__('vitahr.employee'))
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make(
                    'checkInWorkLocation.name'
                )
                    ->label(__('vitahr.work_location'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('check_in_at')
                    ->label(__('vitahr.check_in'))
                    ->dateTime('H:i:s')
                    ->sortable(),

                Tables\Columns\TextColumn::make('check_out_at')
                    ->label(__('vitahr.check_out'))
                    ->dateTime('H:i:s')
                    ->placeholder(__('vitahr.open'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('duration')
                    ->label(__('vitahr.duration'))
                    ->state(
                        function (AttendanceRecord $record): string {
                            if (
                                !$record->check_in_at
                                || !$record->check_out_at
                            ) {
                                return '-';
                            }

                            $seconds =
                                $record->check_in_at
                                    ->diffInSeconds(
                                        $record->check_out_at
                                    );

                            $hours = intdiv(
                                $seconds,
                                3600
                            );

                            $minutes = intdiv(
                                $seconds % 3600,
                                60
                            );

                            return sprintf(
                                '%02d:%02d',
                                $hours,
                                $minutes
                            );
                        }
                    ),

                Tables\Columns\TextColumn::make(
                    'checkInDevice.device_name'
                )
                    ->label(__('vitahr.check_in_device'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make(
                    'checkOutDevice.device_name'
                )
                    ->label(__('vitahr.check_out_device'))
                    ->placeholder('-'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('vitahr.status'))
                    ->state(
                        fn(
                        AttendanceRecord $record
                    ): string =>
                        $record->check_out_at
                        ? __('vitahr.completed')
                        : __('vitahr.open')
                    )
                    ->badge()
                    ->color(
                        fn(
                        AttendanceRecord $record
                    ): string =>
                        $record->check_out_at
                        ? 'success'
                        : 'warning'
                    ),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultSort(
                'check_in_at',
                'desc'
            );
    }

    public function getStats(): array
    {
        return [
            'employees' => $this->getTotalEmployees(),
            'records' => $this->getTotalRecords(),
            'completed' => $this->getTotalCompleted(),
            'open' => $this->getTotalOpen(),
        ];
    }

    public function resetFilters(): void
    {
        $this->employeeIds = [];

        $this->from = now()
            ->startOfMonth()
            ->toDateString();

        $this->until = now()
            ->toDateString();
    }

    public function exportExcel()
    {
        $filename = 'attendance-report';

        if ($this->from && $this->until) {
            $filename .= '-'
                . $this->from
                . '-'
                . $this->until;
        }

        return Excel::download(
            new AttendanceReportExport(
                $this->employeeIds,
                $this->from,
                $this->until
            ),
            $filename . '.xlsx'
        );
    }

    public function exportPdf()
    {
        $export = new AttendanceReportPdfExport(
            $this->employeeIds,
            $this->from,
            $this->until
        );
        $filename = 'attendance-report';

        if ($this->from && $this->until) {
            $filename .= '-'
                . $this->from
                . '-'
                . $this->until;
        }

        $records = $export->records();

        $employeeLabel = __('vitahr.all_employees');

        if (!empty($this->employeeIds)) {
            $employeeLabel = Employee::query()
                ->with('user')
                ->whereIn(
                    'id',
                    $this->employeeIds
                )
                ->orderBy('employee_number')
                ->get()
                ->map(
                    fn(Employee $employee): string =>
                    $employee->employee_number
                    . ' — '
                    . ($employee->user?->name ?? '-')
                )
                ->implode(', ');
        }

        $totalRecords = $records->count();

        $totalCompleted = $records
            ->whereNotNull('check_out_at')
            ->count();

        $totalOpen = $records
            ->whereNull('check_out_at')
            ->count();

        $totalEmployees = $records
            ->pluck('employee_id')
            ->unique()
            ->count();

        $defaultConfig = (new ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4-L',
            'direction' => app()->getLocale() === 'ar' ? 'rtl' : 'ltr',

            'autoScriptToLang' => true,
            'autoLangToFont' => true,

            'fontDir' => array_merge($fontDirs, [
                storage_path('fonts'),
            ]),

            'fontdata' => $fontData + [
                'almarai' => [
                    'R' => 'Almarai-Regular.ttf',
                    'B' => 'Almarai-ExtraBold.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ],
            ],

            'default_font' => 'almarai',

            'margin_top' => 15,
            'margin_bottom' => 15,
            'margin_left' => 10,
            'margin_right' => 10,

            'tempDir' => storage_path('app/mpdf/tmp'),

            'allow_output_buffering' => true,
        ]);

        $html = view(
            'filament.vitahr.reports.attendance-report-pdf',
            [
                'records' => $records,
                'from' => $this->from,
                'until' => $this->until,
                'employeeLabel' => $employeeLabel,
                'totalRecords' => $totalRecords,
                'totalCompleted' => $totalCompleted,
                'totalOpen' => $totalOpen,
                'totalEmployees' => $totalEmployees,
            ]
        )->render();

        $mpdf->WriteHTML($html);

        return response()->streamDownload(
            fn() => print $mpdf->Output('', 'S'),
            $filename . '.pdf'
        );
    }
}
