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
            $data = [
                'record' => $record,
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
                'margin_bottom' => 15,
                'margin_left' => 8,
                'margin_right' => 8,
                'tempDir' => storage_path('app/mpdf/tmp'),
                'allow_output_buffering' => true,
            ]);

            // تذييل الصفحة
            $mpdf->SetHTMLFooter('
                <div style="text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 5px;">
                    الصفحة {PAGENO} من {nbpg} | ' . date('Y-m-d H:i') . '
                </div>
            ');

            $html = view('filament.pages.contract-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
