<?php

namespace App\Filament\Actions;

use App\Models\ProjectContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;

class ExportContractToPdfAction
{
    public static function make(): Action
    {
        return Action::make('exportPdf')
            ->label('📄 تصدير PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function (ProjectContract $record) {
                return static::exportToPdf($record);
            });
    }

    public static function exportToPdf(ProjectContract $record): StreamedResponse
    {
        $filename = "عقد_بناء_{$record->id}_{$record->contract_date->format('Y-m-d')}.pdf";

        return new StreamedResponse(function () use ($record) {
            // Prepare variables for content replacement
            $variables = self::prepareVariables($record);

            // Process all content fields with variables
            $processedContents = self::processContentFields($record, $variables);

            $data = [
                'record' => $record,
                'contents' => $processedContents, // Pass processed contents to view
                'logo' => 'file://' . public_path('images/alrayan-logo2025.png'),
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
                'margin_left' => 4,
                'margin_right' => 4,
                'tempDir' => storage_path('app/mpdf/tmp'),
                'allow_output_buffering' => true,
            ]);

            $footerContent = '
    <div style="position: fixed; bottom: 0; left: 0; right: 0; text-align: center;">
        <img src="file://' . public_path('images/letterhead.png') . '" style="width: 100%; max-height: 338px; opacity: 1;" />
    </div>
';

$mpdf->SetHTMLFooter($footerContent);
            // // تذييل الصفحة
            // $mpdf->SetHTMLFooter('
            //     <div style="text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 5px;">
            //         الصفحة {PAGENO} من {nbpg} | ' . date('Y-m-d H:i') . '
            //     </div>
            // ');

            $html = view('filament.pages.contract-pdf', $data)->render();
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
    private static function prepareVariables(ProjectContract $record): array
    {
        $currencySymbol = $record->currency->symbol ?? 'ريال';
        $currencyName = $record->currency->name ?? 'ريال سعودي';


        dd( [
            // Basic information
            'project_location' => $record->project_location,
            'owner_name' => $record->owner_name,
            'owner_id_number' => $record->owner_id_number,
            'owner_address' => $record->owner_address,
            'owner_phone' => $record->owner_phone,
            'contractor_name' => $record->contractor_name,
            'contractor_commercial_registration' => $record->contractor_commercial_registration,
            'contractor_address' => $record->contractor_address,
            'contractor_phone' => $record->contractor_phone,

            // Project details
            'execution_period' => $record->execution_period,
            'delay_penalty_percentage' => $record->delay_penalty_percentage,
            'max_penalty_percentage' => $record->max_penalty_percentage,
            'arbitration_location' => $record->arbitration_location,

            // Financial details
            'total_contract_value' => $record->total_contract_value,
            'total_contract_value_formatted' => number_format($record->total_contract_value, 2),
            'currency_symbol' => $currencySymbol,
            'currency_name' => $currencyName,
            'initial_payment_percentage' => $record->initial_payment_percentage,
            'concrete_stage_payment_percentage' => $record->concrete_stage_payment_percentage,
            'finishing_stage_payment_percentage' => $record->finishing_stage_payment_percentage,
            'final_payment_percentage' => $record->final_payment_percentage,

            // Contract details
            'contract_date' => $record->contract_date->format('d/m/Y'),
            'contract_number' => 'CONTRACT-' . $record->id,

            // Calculated values
            'initial_payment_amount' => number_format(($record->total_contract_value * $record->initial_payment_percentage) / 100, 2),
            'concrete_stage_payment_amount' => number_format(($record->total_contract_value * $record->concrete_stage_payment_percentage) / 100, 2),
            'finishing_stage_payment_amount' => number_format(($record->total_contract_value * $record->finishing_stage_payment_percentage) / 100, 2),
            'final_payment_amount' => number_format(($record->total_contract_value * $record->final_payment_percentage) / 100, 2),
        ]);

        return [
            // Basic information
            'project_location' => $record->project_location,
            'owner_name' => $record->owner_name,
            'owner_id_number' => $record->owner_id_number,
            'owner_address' => $record->owner_address,
            'owner_phone' => $record->owner_phone,
            'contractor_name' => $record->contractor_name,
            'contractor_commercial_registration' => $record->contractor_commercial_registration,
            'contractor_address' => $record->contractor_address,
            'contractor_phone' => $record->contractor_phone,

            // Project details
            'execution_period' => $record->execution_period,
            'delay_penalty_percentage' => $record->delay_penalty_percentage,
            'max_penalty_percentage' => $record->max_penalty_percentage,
            'arbitration_location' => $record->arbitration_location,

            // Financial details
            'total_contract_value' => $record->total_contract_value,
            'total_contract_value_formatted' => number_format($record->total_contract_value, 2),
            'currency_symbol' => $currencySymbol,
            'currency_name' => $currencyName,
            'initial_payment_percentage' => $record->initial_payment_percentage,
            'concrete_stage_payment_percentage' => $record->concrete_stage_payment_percentage,
            'finishing_stage_payment_percentage' => $record->finishing_stage_payment_percentage,
            'final_payment_percentage' => $record->final_payment_percentage,

            // Contract details
            'contract_date' => $record->contract_date->format('d/m/Y'),
            'contract_number' => 'CONTRACT-' . $record->id,

            // Calculated values
            'initial_payment_amount' => number_format(($record->total_contract_value * $record->initial_payment_percentage) / 100, 2),
            'concrete_stage_payment_amount' => number_format(($record->total_contract_value * $record->concrete_stage_payment_percentage) / 100, 2),
            'finishing_stage_payment_amount' => number_format(($record->total_contract_value * $record->finishing_stage_payment_percentage) / 100, 2),
            'final_payment_amount' => number_format(($record->total_contract_value * $record->final_payment_percentage) / 100, 2),
        ];
    }

    /**
     * Process all content fields and replace variables
     */
    private static function processContentFields(ProjectContract $record, array $variables): array
    {
        $fields = [
            'preamble_content' => null,
            'subject_content' => null,
            'specifications_content' => null,
            'duration_content' => null,
            'payment_content' => null,
            'obligations_content' => null,
            'warranty_content' => null,
            'termination_content' => null,
            'arbitration_content' => null,
            'general_terms_content' => null,
            'notes_content' => null,
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
     * Supports multiple variable formats:
     * - {{ $variable }}
     * - {variable}
     * - $variable
     */
    private static function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            // Replace different variable formats
            $patterns = [
                '/\{\{\s*\\$' . $key . '\s*\}\}/',     // {{ $variable }}
                '/\{\{\s*\\$' . $key . '\.?\s*\}\}/',  // {{ $variable. }}
                '/\{' . $key . '\}/',                  // {variable}
                '/\\$' . $key . '/',                   // $variable
                '/\{\{\s*' . $key . '\s*\}\}/',        // {{ variable }}
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

    /**
     * Alternative: If you want to use a template engine approach
     */
    private static function replaceVariablesUsingBlade(string $content, array $variables): string
    {
        // This is a simpler approach but requires more setup
        $blade = app('blade.compiler');

        // Create a temporary file with the content
        $tempFile = tempnam(sys_get_temp_dir(), 'contract_');
        file_put_contents($tempFile, $content);

        try {
            // Compile the blade template with variables
            $compiled = $blade->compileString(file_get_contents($tempFile));

            // Extract the PHP code and evaluate it
            ob_start();
            extract($variables, EXTR_SKIP);
            eval('?>' . $compiled);
            $result = ob_get_clean();

            return $result;
        } catch (\Exception $e) {
            // Fallback to simple replacement
            return self::replaceVariables($content, $variables);
        } finally {
            @unlink($tempFile);
        }
    }
}
