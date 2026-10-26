<?php

namespace App\Filament\Actions;

use App\Models\EmployeeContractNew;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class ExportEmployeeContractNewToPdfAction
{
    public static function make(): Action
    {
        return Action::make('exportPdf')
            ->label('📄 تصدير PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function (EmployeeContractNew $record) {
                return static::exportToPdf($record);
            });
    }

    public static function exportToPdf(EmployeeContractNew $record): StreamedResponse
    {
        $startDate = $record->start_date;
        $filename = "عقد_موظف_{$record->employee_id}_" . ($startDate ? (is_string($startDate) ? $startDate : $startDate->toDateString()) : 'unknown') . ".pdf";

        return new StreamedResponse(function () use ($record) {
            // Prepare variables for content replacement
            $variables = self::prepareVariables($record);

            // Process all content fields with variables
            $processedContents = self::processContentFields($record, $variables);

            $data = [
                'record' => $record,
                'contents' => $processedContents,
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

            $footerContent = '<div style="position: absolute; bottom: 15px; left: 0; right: 0; width: 100%; margin: 0; padding: 0;">
                    <img src="file://' . public_path('images/new-footer.png') . '" style="width: 100%; height: auto; display: block; margin: 0; padding: 0;" />
                </div>';

            $mpdf->SetHTMLFooter($footerContent);

            $html = view('filament.pages.employee-contract-new-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Prepare all variables for content replacement
     */
    private static function prepareVariables(EmployeeContractNew $record): array
    {
        $employee = $record->employee;
        $currencySymbol = 'ليرة سورية جديدة';
        $currencyName = 'ليرة سورية جديدة';

        $variables = [
            // Employee information (from related employee)
            'employee_name' => $employee ? $employee->name_ar : 'غير محدد',
            'employee_id_number' => $employee ? $employee->national_id : 'غير محدد',
            'employee_address' => $employee ? $employee->current_address : 'غير محدد',
            'employee_phone' => $employee ? $employee->phone : 'غير محدد',
            'employee_email' => $employee ? $employee->email : 'غير محدد',
            'employee_nationality' => $employee ? $employee->nationality : 'غير محدد',

            // Job information (from related employee)
            'job_title' => $employee && $employee->jobTitle ? $employee->jobTitle->name_ar : 'غير محدد',
            'department' => $employee && $employee->department ? $employee->department->name_ar : 'غير محدد',
            'job_description' => 'غير محدد',

            // Company information (from contract)
            'company_name' => $record->company_name ?? 'غير محدد',
            'company_commercial_registration' => $record->company_commercial_registration ?? 'غير محدد',
            'company_registration_date' => $record->company_registration_date ? (is_string($record->company_registration_date) ? $record->company_registration_date : $record->company_registration_date->toDateString()) : 'غير محدد',
            'company_registration_source' => $record->company_registration_source ?? 'غير محدد',
            'company_general_manager_name' => $record->company_general_manager_name ?? 'غير محدد',
            'company_representative_name' => $record->company_representative_name ?? 'غير محدد',
            'company_address' => $record->company_address ?? 'غير محدد',
            'company_phone' => $record->company_phone ?? 'غير محدد',

            // Salary details (from employee)
            'basic_salary' => $employee ? (float) $employee->basic_salary : 0,
            'basic_salary_formatted' => $employee ? number_format((float) $employee->basic_salary, 2) : '0.00',
            'housing_allowance' => $employee ? (float) $employee->housing_allowance : 0,
            'housing_allowance_formatted' => $employee ? number_format((float) $employee->housing_allowance, 2) : '0.00',
            'transportation_allowance' => $employee ? (float) $employee->transportation_allowance : 0,
            'transportation_allowance_formatted' => $employee ? number_format((float) $employee->transportation_allowance, 2) : '0.00',
            'other_allowances' => $employee ? (float) $employee->other_allowances : 0,
            'other_allowances_formatted' => $employee ? number_format((float) $employee->other_allowances, 2) : '0.00',
            'total_salary' => $employee ? (float) $employee->total_salary : 0,
            'total_salary_formatted' => $employee ? number_format((float) $employee->total_salary, 2) : '0.00',
            'currency_symbol' => $currencySymbol,
            'currency_name' => $currencyName,

            // Contract duration
            'start_date' => $record->start_date ? (is_string($record->start_date) ? $record->start_date : $record->start_date->toDateString()) : 'غير محدد',
            'end_date' => $record->end_date ? (is_string($record->end_date) ? $record->end_date : $record->end_date->toDateString()) : 'غير محدد',
            'probation_period_days' => $record->probation_period_days,

            // Working hours
            'working_hours' => $record->working_hours,
            'working_days' => $record->working_days,

            // Contract details
            'contract_date' => $record->start_date ? (is_string($record->start_date) ? $record->start_date : $record->start_date->toDateString()) : 'غير محدد',
            'contract_number' => 'EMP-CONTRACT-' . $record->id,
        ];

        return $variables;
    }

    /**
     * Process all content fields and replace variables
     */
    private static function processContentFields(EmployeeContractNew $record, array $variables): array
    {
        $fields = [
            'job_desc' => null,
            'con_dur' => null,
            'test_dur' => null,
            'sal_con' => null,
            'leave' => null,
            'vacation' => null,
            'overtime' => null,
            'conditions' => null,
            'renew' => null,
            'system_notes' => null,
            'no_copies' => null,
        ];

        $processedContents = [];

        foreach ($fields as $field => $defaultValue) {
            $content = $record->$field;

            if ($content) {
                // Replace variables in the content
                $processedContents[$field] = self::replaceVariables($content, $variables);
            } else {
                $processedContents[$field] = null;
            }
        }
        return $processedContents;
    }

    /**
     * Replace variables in content string
     */
    private static function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            // Replace different variable formats
            $patterns = [
                '/\{\{\s*\$' . $key . '\s*\}\}/',
                '/\{\{\s*\$' . $key . '\.?\s*\}\}/',
                '/\{' . $key . '\}/',
                '/\$' . $key . '/',
                '/\{\{\s*' . $key . '\s*\}\}/',
            ];

            foreach ($patterns as $pattern) {
                $content = preg_replace($pattern, $value, $content);
            }

            // Also replace with and without underscores
            $keyWithoutUnderscores = str_replace('_', '', $key);
            if ($keyWithoutUnderscores !== $key) {
                $patterns = [
                    '/\{\{\s*\$' . $keyWithoutUnderscores . '\s*\}\}/',
                    '/\{' . $keyWithoutUnderscores . '\}/',
                    '/\$' . $keyWithoutUnderscores . '/',
                ];

                foreach ($patterns as $pattern) {
                    $content = preg_replace($pattern, $value, $content);
                }
            }
        }
        return $content;
    }
}
