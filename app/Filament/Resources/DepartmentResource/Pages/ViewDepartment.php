<?php

namespace App\Filament\Resources\DepartmentResource\Pages;

use App\Filament\Resources\AttendanceResource;
use App\Filament\Resources\DepartmentResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Attendance;
use App\Models\Department;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class ViewDepartment extends ViewRecord
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('record_attendance')
                ->label('تسجيل حضور القسم')
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->url(fn () => AttendanceResource::getUrl('bulk', ['department_id' => $this->record->id])),
            Actions\Action::make('exportDepartmentAttendancePdf')
                ->label('📄 تقرير دوام القسم PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('warning')
                ->form([
                    DatePicker::make('start_date')
                        ->label('من تاريخ')
                        ->required()
                        ->default(now()->subDays(30))
                        ->displayFormat('d/m/Y')
                        ->firstDayOfWeek(7)
                        ->native(false),
                    DatePicker::make('end_date')
                        ->label('إلى تاريخ')
                        ->required()
                        ->default(now())
                        ->displayFormat('d/m/Y')
                        ->firstDayOfWeek(7)
                        ->native(false)
                        ->after('start_date'),
                ])
                ->action(function (Department $record, array $data) {
                    return $this->exportToPdf($record, $data);
                }),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('معلومات القسم')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('name_ar')
                                    ->label('اسم القسم (عربي)')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('name_en')
                                    ->label('اسم القسم (إنكليزي)')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('code')
                                    ->label('رمز القسم')
                                    ->badge()
                                    ->color('primary'),
                                Components\TextEntry::make('manager.name_ar')
                                    ->label('مدير القسم')
                                    ->placeholder('غير محدد'),
                                Components\TextEntry::make('location')
                                    ->label('الموقع')
                                    ->placeholder('غير محدد'),
                                Components\TextEntry::make('phone')
                                    ->label('رقم الهاتف')
                                    ->icon('heroicon-o-phone')
                                    ->placeholder('غير محدد'),
                            ]),
                    ])
                    ->columns(2),

                Components\Section::make('الإحصائيات')
                    ->icon('heroicon-o-chart-bar')
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('employees_count')
                                    ->label('عدد الموظفين')
                                    ->state(fn ($record) => $record->employees()->count())
                                    ->badge()
                                    ->color('success')
                                    ->size('lg'),
                                Components\TextEntry::make('active_employees_count')
                                    ->label('الموظفون النشطون')
                                    ->state(fn ($record) => $record->employees()->where('employment_status', 'active')->count())
                                    ->badge()
                                    ->color('primary'),
                                Components\TextEntry::make('budget')
                                    ->label('الميزانية')
                                    ->money('SYP')
                                    ->placeholder('غير محدد'),
                            ]),
                    ])
                    ->columns(3),

                Components\Section::make('ملاحظات إضافية')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Components\TextEntry::make('description')
                            ->label('الوصف')
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('لا يوجد وصف'),
                    ])
                    ->columns(1),

                Components\Section::make('معلومات النظام')
                    ->icon('heroicon-o-cog')
                    ->collapsible()
                    ->schema([
                        Components\Grid::make(3)
                            ->schema([
                                Components\TextEntry::make('created_at')
                                    ->label('تاريخ الإنشاء')
                                    ->dateTime()
                                    ->since(),
                                Components\TextEntry::make('updated_at')
                                    ->label('آخر تحديث')
                                    ->dateTime()
                                    ->since(),
                                Components\TextEntry::make('deleted_at')
                                    ->label('تاريخ الحذف')
                                    ->dateTime()
                                    ->since()
                                    ->placeholder('غير محذوف'),
                            ]),
                    ])
                    ->columns(3),
            ]);
    }

    private function exportToPdf(Department $department, array $data): StreamedResponse
    {
        $filename = "تقرير_دوام_قسم_{$department->name_ar}_" . now()->format('Y-m-d') . ".pdf";

        return new StreamedResponse(function () use ($department, $data) {
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $endDate = \Carbon\Carbon::parse($data['end_date']);

            // Get attendance records for all employees in this department (selected period)
            $attendances = Attendance::whereHas('employee', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date')
            ->with('employee')
            ->get();

            // Get leave records for all employees in this department (selected period)
            $leaves = \App\Models\Leave::whereHas('employee', function ($query) use ($department) {
                $query->where('department_id', $department->id);
            })
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->orderBy('start_date')
            ->with('employee')
            ->get();

            // Generate all dates in the range
            $dates = [];
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dates[] = $currentDate->copy();
                $currentDate->addDay();
            }

            $viewData = [
                'department' => $department,
                'attendances' => $attendances,
                'leaves' => $leaves,
                'dates' => $dates,
                'logo' => 'file://' . public_path('images/alr-logo-reports.png'),
                'stamp' => 'file://' . public_path('images/stamp.png'),
                'company_name' => 'file://' . public_path('images/name.png'),
            ];

            // إعدادات MPDF
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
                'fontDir' => array_merge($fontDirs, [
                    base_path('vendor/mpdf/mpdf/ttfonts'),
                    storage_path('fonts'),
                ]),
                'fontdata' => [
                    'almarai' => [
                        'R' => 'Almarai-Regular.ttf',
                        'B' => 'Almarai-ExtraBold.ttf',
                        'useOTL' => 0xFF,
                        'useKashida' => 75,
                    ],
                ],
                'default_font' => 'almarai',
                'margin_top' => 10,
                'margin_bottom' => 40,
                'margin_left' => 10,
                'margin_right' => 10,
                'tempDir' => storage_path('app/mpdf/tmp'),
                'allow_output_buffering' => true,
            ]);

            $footerContent = '<div style="position: absolute; bottom: 0; left: 0; right: 0; width: 100%; margin: 0; padding: 0;">
                    <img src="file://' . public_path('images/new-footer.png') . '" style="width: 100%; height: auto; display: block; margin: 0; padding: 0;" />
                </div>';

            $mpdf->SetHTMLFooter($footerContent);

            $html = view('filament.pages.department-attendance-pdf', $viewData)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
