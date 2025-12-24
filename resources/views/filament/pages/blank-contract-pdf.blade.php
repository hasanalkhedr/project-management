<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>عقد اتفاق - {{ $record->title }}</title>
    <link rel="stylesheet" href="{{ public_path('css/reports.css') }}">
    <style>
        @font-face {
            font-family: 'almarai';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path('fonts/Almarai-ExtraBold.ttf') }}') format('truetype');
        }
    </style>
</head>

<body class="contract">
    <!-- Header Section -->
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="logo-cell" rowspan="2">
                    @if (file_exists(public_path('images/alrayan-logo2025.png')))
                        <img src="{{ $logo }}" class="logo" alt="شعار الشركة" />
                    @endif
                </td>
            </tr>
            <tr>
                <td class="title-cell">
                    <p class="contract-title">عقد اتفاق</p>
                    <p class="contract-title">{{ $record->title }}</p>
                </td>
                <td class="right-cell" rowspan="1"></td>
            </tr>
            <tr>
                <td class="content-cell" colspan="2">
                    <div class="contract-meta">
                        العقد رقم: CONTRACT-{{ $record->id }} &nbsp;&nbsp;&nbsp;&nbsp;|&nbsp;&nbsp;&nbsp;&nbsp;
                        التاريخ: {{ now()->format('d/m/Y') }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div>
        {!! $record->contents !!}
    </div>

    <!-- التوقيعات -->
    <div class="signature-section no-break">
        <table class="signature-table">
            <tr>
                <td class="signature-cell">
                    <div class="text-bold">توقيع الطرف الأول</div>
                    <div class="signature-line"></div>
                    <div class="stamp-placeholder">
                        <br /><br /><br /><br /><br /><br /><br />
                    </div>
                </td>

                <td class="signature-cell">
                    <div class="text-bold">توقيع الطرف الثاني</div>
                    <div class="signature-line"></div>
                    <div class="stamp-placeholder">
                        <br /><br /><br /><br /><br /><br /><br />
                    </div>
                </td>
            </tr>
        </table>
    </div>

    <div class="final-statement">
        حرر هذا العقد من نسختين أصليتين بيد كل طرف نسخة للعمل بموجبها
    </div>

    {{-- <div class="footer">
        <div>شركة أبراج الريان للمقاولات</div>
        <div>https://alrayanrealestate.com/ - © {{ date('Y') }} جميع الحقوق محفوظة</div>
    </div> --}}
</body>

</html>
