<?php

namespace App\Filament\Actions;

use App\Models\Attendance;
use App\Models\Employee;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class ExportEmployeeAttendanceToPdfAction
{
    public static function make(): Action
    {
        return Action::make('exportAttendancePdf')
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
                return static::exportToPdf($record, $data);
            });
    }

    public static function exportToPdf(Employee $employee, array $data): StreamedResponse
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
