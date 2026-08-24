<?php

namespace App\Filament\Resources\EmployeeResource\Pages;

use App\Filament\Resources\EmployeeContractNewResource;
use App\Filament\Resources\EmployeeResource;
use App\Filament\Resources\AttendanceResource;
use Filament\Actions;
use Filament\Forms\Components\DatePicker;
use Filament\Infolists;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use App\Models\Attendance;
use App\Models\Employee;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class ViewEmployee extends ViewRecord
{
    protected static string $resource = EmployeeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            Actions\Action::make('create_contract')
                ->label('إنشاء عقد')
                ->icon('heroicon-o-document')
                ->color('success')
                ->url(fn () => EmployeeContractNewResource::getUrl('create', ['employee_id' => $this->record->id]))
                ->openUrlInNewTab()
                ->hidden(fn () => $this->record->contracts()->exists()),
            Actions\Action::make('view_contracts')
                ->label('عرض العقود')
                ->icon('heroicon-o-folder-open')
                ->color('info')
                ->url(fn () => EmployeeContractNewResource::getUrl('index'))
                ->openUrlInNewTab(),
            Actions\Action::make('record_attendance')
                ->label('تسجيل حضور')
                ->icon('heroicon-o-clock')
                ->color('primary')
                ->url(fn () => AttendanceResource::getUrl('bulk', ['employee_id' => $this->record->id])),
            Actions\Action::make('exportAttendancePdf')
                ->label('📄 تقرير الدوام PDF')
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
                ->action(function (Employee $record, array $data) {
                    return $this->exportToPdf($record, $data);
                }),
            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('معلومات الموظف')
                    ->schema([
                        Components\Split::make([
                            Components\ImageEntry::make('photo')
                                ->label('الصورة الشخصية')
                                ->circular()
                                ->size(150)
                                ->defaultImageUrl(url('/images/default-avatar.png'))
                                ->columnSpan(1),
                            Components\Grid::make(2)
                                ->schema([
                                    Components\TextEntry::make('employee_number')
                                        ->label('رقم الموظف')
                                        ->badge()
                                        ->color('primary'),
                                    Components\TextEntry::make('name_ar')
                                        ->label('الاسم الكامل (عربي)')
                                        ->size('lg')
                                        ->weight('bold')
                                        ->columnSpanFull(),
                                    Components\TextEntry::make('name_en')
                                        ->label('الاسم الكامل (إنكليزي)')
                                        ->columnSpanFull(),
                                    Components\TextEntry::make('nationality')
                                        ->label('الجنسية')
                                        ->formatStateUsing(fn ($state) => $state === 'syrian' ? 'سوري' : 'أخرى'),
                                    Components\TextEntry::make('date_of_birth')
                                        ->label('تاريخ الميلاد')
                                        ->date(),
                                    Components\TextEntry::make('gender')
                                        ->label('الجنس')
                                        ->formatStateUsing(fn ($state) => $state === 'male' ? 'ذكر' : 'أنثى'),
                                    Components\TextEntry::make('marital_status')
                                        ->label('الحالة الاجتماعية')
                                        ->formatStateUsing(fn ($state) => match ($state) {
                                            'single' => 'عازب',
                                            'married' => 'متزوج',
                                            'divorced' => 'مطلق',
                                            'widowed' => 'أرمل',
                                            default => $state,
                                        }),
                                    Components\TextEntry::make('national_id')
                                        ->label('الرقم الوطني')
                                        ->copyable(),
                                    Components\TextEntry::make('place_of_registration')
                                        ->label('مكان القيد'),
                                ])
                                ->columnSpan(2),
                        ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Components\Section::make('معلومات التواصل')
                    ->icon('heroicon-o-phone')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('phone')
                                    ->label('رقم الهاتف')
                                    ->icon('heroicon-o-phone')
                                    ->copyable(),
                                Components\TextEntry::make('email')
                                    ->label('البريد الإلكتروني')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable(),
                                Components\TextEntry::make('current_address')
                                    ->label('العنوان الحالي')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('emergency_contact_name')
                                    ->label('شخص للطوارئ'),
                                Components\TextEntry::make('emergency_contact_phone')
                                    ->label('رقم هاتف الطوارئ')
                                    ->icon('heroicon-o-phone'),
                            ]),
                    ])
                    ->columns(2),

                Components\Section::make('معلومات العمل')
                    ->icon('heroicon-o-briefcase')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('department')
                                    ->label('القسم')
                                    ->badge()
                                    ->color('success'),
                                Components\TextEntry::make('job_title')
                                    ->label('المسمى الوظيفي')
                                    ->badge()
                                    ->color('info'),
                                Components\TextEntry::make('directManager.name_ar')
                                    ->label('المدير المباشر')
                                    ->placeholder('لا يوجد'),
                                Components\TextEntry::make('project.name')
                                    ->label('المشروع المرتبط')
                                    ->placeholder('لا يوجد'),
                                Components\TextEntry::make('contract_type')
                                    ->label('نوع العقد')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'full_time' => 'دوام كامل',
                                        'part_time' => 'دوام جزئي',
                                        'daily' => 'يومي',
                                        'contractor' => 'مقاول',
                                        default => $state,
                                    })
                                    ->badge()
                                    ->color('warning'),
                                Components\TextEntry::make('employment_status')
                                    ->label('حالة الموظف')
                                    ->formatStateUsing(fn ($state) => match ($state) {
                                        'active' => 'نشط',
                                        'suspended' => 'موقوف',
                                        'terminated' => 'منتهي',
                                        default => $state,
                                    })
                                    ->badge()
                                    ->color(fn ($state) => match ($state) {
                                        'active' => 'success',
                                        'suspended' => 'warning',
                                        'terminated' => 'danger',
                                        default => 'gray',
                                    }),
                                Components\TextEntry::make('hire_date')
                                    ->label('تاريخ التوظيف')
                                    ->date()
                                    ->icon('heroicon-o-calendar'),
                                Components\TextEntry::make('probation_period')
                                    ->label('فترة التجربة (أيام)')
                                    ->suffix(' يوم'),
                                Components\TextEntry::make('contract_start_date')
                                    ->label('تاريخ بداية العقد')
                                    ->date(),
                                Components\TextEntry::make('contract_end_date')
                                    ->label('تاريخ نهاية العقد')
                                    ->date()
                                    ->color(fn ($record) => $record->contract_end_date && $record->contract_end_date < now() ? 'danger' : 'default'),
                            ]),
                    ])
                    ->columns(2),

                Components\Section::make('الراتب والبدلات')
                    ->icon('heroicon-o-currency-dollar')
                    ->schema([
                        Components\Grid::make(2)
                            ->schema([
                                Components\TextEntry::make('basic_salary')
                                    ->label('الراتب الأساسي')
                                    ->money('SYP')
                                    ->size('lg')
                                    ->weight('bold')
                                    ->color('success'),
                                Components\TextEntry::make('housing_allowance')
                                    ->label('بدل السكن')
                                    ->money('SYP'),
                                Components\TextEntry::make('transportation_allowance')
                                    ->label('بدل المواصلات')
                                    ->money('SYP'),
                                Components\TextEntry::make('other_allowances')
                                    ->label('بدلات أخرى')
                                    ->money('SYP'),
                                Components\TextEntry::make('total_salary')
                                    ->label('إجمالي الراتب')
                                    ->money('SYP')
                                    ->size('xl')
                                    ->weight('bold')
                                    ->color('primary')
                                    ->columnSpanFull(),
                                Components\TextEntry::make('payment_method')
                                    ->label('طريقة الدفع')
                                    ->formatStateUsing(fn ($state) => $state === 'cash' ? 'نقدي' : 'تحويل بنكي')
                                    ->badge()
                                    ->color('info'),
                                Components\TextEntry::make('bank_account_number')
                                    ->label('رقم الحساب البنكي')
                                    ->copyable()
                                    ->placeholder('لا يوجد'),
                                Components\TextEntry::make('bank_name')
                                    ->label('اسم البنك')
                                    ->placeholder('لا يوجد'),
                                Components\TextEntry::make('social_security_number')
                                    ->label('رقم الضمان الاجتماعي')
                                    ->copyable()
                                    ->placeholder('لا يوجد'),
                            ]),
                    ])
                    ->columns(2),

                Components\Section::make('ملاحظات إضافية')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Components\TextEntry::make('notes')
                            ->label('ملاحظات')
                            ->markdown()
                            ->columnSpanFull()
                            ->placeholder('لا توجد ملاحظات'),
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

    private function exportToPdf(Employee $employee, array $data): StreamedResponse
    {
        $filename = "تقرير_دوام_{$employee->name_ar}_" . now()->format('Y-m-d') . ".pdf";

        return new StreamedResponse(function () use ($employee, $data) {
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            $endDate = \Carbon\Carbon::parse($data['end_date']);

            // Get attendance records for the selected period
            $attendances = Attendance::where('employee_id', $employee->id)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            // Get leave records for the selected period
            $leaves = \App\Models\Leave::where('employee_id', $employee->id)
                ->where('start_date', '<=', $endDate)
                ->where('end_date', '>=', $startDate)
                ->orderBy('start_date')
                ->get();

            // Generate all dates in the range
            $dates = [];
            $currentDate = $startDate->copy();
            while ($currentDate->lte($endDate)) {
                $dates[] = $currentDate->copy();
                $currentDate->addDay();
            }

            $viewData = [
                'employee' => $employee,
                'attendances' => $attendances,
                'leaves' => $leaves,
                'dates' => $dates,
                'logo' => 'file://' . public_path('images/alrayan-logo2026.png'),
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

            $html = view('filament.pages.employee-attendance-pdf', $viewData)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
