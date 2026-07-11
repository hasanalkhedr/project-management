<?php

namespace App\Filament\Actions;

use App\Models\BlankContract;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
class ExportBlankContractToPdfAction
{
    public static function make(): Action
    {
        return Action::make('exportPdf')
            ->label('📄 تصدير PDF')
            ->icon('heroicon-o-document-arrow-down')
            ->color('success')
            ->action(function (BlankContract $record) {
                return static::exportToPdf($record);
            });
    }

    public static function exportToPdf(BlankContract $record): StreamedResponse
    {
        $filename = "عقد_اتفاق_{$record->title}_". now()->format('Y-m-d') .".pdf";

        return new StreamedResponse(function () use ($record) {
            $data = [
                'record' => $record,
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
            // تذييل الصفحة
            // $mpdf->SetHTMLFooter('
            //     <div style="text-align: center; font-size: 10px; color: #666; border-top: 1px solid #ddd; padding-top: 5px;">
            //         الصفحة {PAGENO} من {nbpg} | ' . date('Y-m-d H:i') . '
            //     </div>
            // ');

            $html = view('filament.pages.blank-contract-pdf', $data)->render();
            $mpdf->WriteHTML($html);
            $mpdf->Output('', 'I');
        }, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
