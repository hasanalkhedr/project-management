<?php

namespace App\Filament\Actions;

use App\Models\EmployeeContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class ExportEmployeeContractToPdfAction
{
    public static function make(): Action
    {
        return Action::make('exportPdf')
            ->label('📄 تصدير PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function (EmployeeContract $record) {
                return static::exportToPdf($record);
            });
    }

    public static function exportToPdf(EmployeeContract $record): StreamedResponse
    {
        $filename = "عقد_موظف_{$record->id}_" . ($record->contract_date ? $record->contract_date->format('Y-m-d') : 'unknown') . ".pdf";

        return new StreamedResponse(function () use ($record) {
            // Prepare variables for content replacement
            $variables = self::prepareVariables($record);

            // Process all content fields with variables
            $processedContents = self::processContentFields($record, $variables);

            $data = [
                'record' => $record,
                'contents' => $processedContents,
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

            $footerContent = '<div style="position: absolute; bottom: 15px; left: 0; right: 0; width: 100%; margin: 0; padding: 0;">
                    <img src="file://' . public_path('images/new-footer.png') . '" style="width: 100%; height: auto; display: block; margin: 0; padding: 0;" />
                </div>';

            $mpdf->SetHTMLFooter($footerContent);

            $html = view('filament.pages.employee-contract-pdf', $data)->render();
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
    private static function prepareVariables(EmployeeContract $record): array
    {
        $currencySymbol =  'ليرة سورية جديدة';
        $currencyName =  'ليرة سورية جديدة';

        return [
            // Employee information
            'employee_name' => $record->employee_name,
            'employee_id_number' => $record->employee_id_number,
            'employee_address' => $record->employee_address,
            'employee_phone' => $record->employee_phone,
            'employee_email' => $record->employee_email,

            // Job information
            'job_title' => $record->job_title,
            'department' => $record->department,
            'job_description' => $record->job_description,

            // Company information
            'company_name' => $record->company_name,
            'company_commercial_registration' => $record->company_commercial_registration,
            'company_registration_date' => $record->company_registration_date,
            'company_registration_source' => $record->company_registration_source,
            'company_general_manager_name' => $record->company_general_manager_name,
            'company_representative_name' => $record->company_representative_name,
            'company_address' => $record->company_address,
            'company_phone' => $record->company_phone,

            // Salary details
            'basic_salary' => (float) $record->basic_salary,
            'basic_salary_formatted' => number_format((float) $record->basic_salary, 2),
            'salary_usd' => (float) $record->basic_salary_usd,
            'salary_usd_formatted' => number_format((float) $record->basic_salary_usd, 2),
            // 'housing_allowance' => (float) $record->housing_allowance,
            // 'housing_allowance_formatted' => number_format((float) $record->housing_allowance, 2),
            // 'transportation_allowance' => (float) $record->transportation_allowance,
            // 'transportation_allowance_formatted' => number_format((float) $record->transportation_allowance, 2),
            // 'other_allowances' => (float) $record->other_allowances,
            // 'other_allowances_formatted' => number_format((float) $record->other_allowances, 2),
            // 'total_salary' => (float) $record->total_salary,
            // 'total_salary_formatted' => number_format((float) $record->total_salary, 2),
            'currency_symbol' => $currencySymbol,
            'currency_name' => $currencyName,

            // Contract duration
            // 'start_date' => $record->start_date ? $record->start_date->format('d/m/Y') : 'غير محدد',
            // 'end_date' => $record->end_date ? $record->end_date->format('d/m/Y') : 'غير محدد',
            // 'probation_period_days' => $record->probation_period_days,

            // // Working hours
            // 'working_hours' => $record->working_hours,
            // 'working_days' => $record->working_days,

            // Contract details
            'contract_date' => $record->contract_date ? $record->contract_date->format('d/m/Y') : 'غير محدد',
            'contract_number' => 'EMP-CONTRACT-' . $record->id,
        ];
    }

    /**
     * Process all content fields and replace variables
     */
    private static function processContentFields(EmployeeContract $record, array $variables): array
    {
        $fields = [
            'preamble_content' => null,
            // 'subject_content' => null,
            // 'responsibilities_content' => null,
            // 'working_hours_content' => null,
            // 'salary_content' => null,
            // 'benefits_content' => null,
            // 'leave_content' => null,
            // 'termination_content' => null,
            // 'confidentiality_content' => null,
            // 'general_terms_content' => null,

            'job_desc' => null,
            'con_dur' => null,
            'test_dur' => null,
            'start_date' => null,
            'sal_con' => null,
            'leave' => null,
            'vacation' => null,
            'overtime' => null,
            'working_hours' => null,
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
                '/\{\{\s*\\$' . $key . '\s*\}\}/',
                '/\{\{\s*\\$' . $key . '\.?\s*\}\}/',
                '/\{' . $key . '\}/',
                '/\\$' . $key . '/',
                '/\{\{\s*' . $key . '\s*\}\}/',
            ];

            foreach ($patterns as $pattern) {
                $content = preg_replace($pattern, $value, $content);
            }

            // Also replace with and without underscores
            $keyWithoutUnderscores = str_replace('_', '', $key);
            if ($keyWithoutUnderscores !== $key) {
                $patterns = [
                    '/\{\{\s*\\$' . $keyWithoutUnderscores . '\s*\}\}/',
                    '/\{' . $keyWithoutUnderscores . '\}/',
                    '/\\$' . $keyWithoutUnderscores . '/',
                ];

                foreach ($patterns as $pattern) {
                    $content = preg_replace($pattern, $value, $content);
                }
            }
        }

        return $content;
    }
}
