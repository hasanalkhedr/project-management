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

        body {
            font-family: 'almarai', sans-serif;
            color: #1a2b3c;
            margin: 0;
            padding: 0;
        }

        /* ترويسة الصفحة */
        .header-container {
            width: 100%;
            margin-bottom: 20px;
        }

        /* الشعار في أعلى اليسار */
        .logo-wrapper {
            text-align: left;
            margin-bottom: 10px;
        }

        .logo-wrapper img {
            max-height: 75px;
            width: auto;
        }

        /* عنوان العقد بالمنتصف */
        .main-title {
            text-align: center;
            font-size: 30px;
            font-weight: bold;
            color: #0f3d3e;
            margin: 10px 0 10px 0;
        }

        /* صندوق رقم العقد والتاريخ */
        .meta-box {
            border: 0px solid #2b4c59;
            border-radius: 8px;
            padding: 3px 3px;
            margin: 0 auto 10px auto;
            width: 95%;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-table td {
            vertical-align: middle;
            font-size: 14px;
        }

        .meta-right {
            text-align: center;
            width: 45%;
        }

        .meta-divider {
            text-align: center;
            width: 10%;
            color: #7f8c8d;
            font-size: 16px;
        }

        .meta-left {
            text-align: center;
            width: 45%;
        }

        .label-text {
            font-weight: bold;
            color: #1a2b3c;
            margin-left: 8px;
            text-align: center;
        }

        .value-text {
            color: #2c3e50;
            font-weight: bold;
            text-align: center;
        }

        /* الخط الفاصل السفلي */
        .header-line {
            border-bottom: 2px solid #2b4c59;
            margin-bottom: 5px;
            width: 100%;
        }
    </style>
</head>

<body class="contract">
    <!-- Header Section -->
    <div class="header-container">
        <!-- الشعار أعلى اليسار -->
        <div class="logo-cell">
            <img src="{{ $logo }}" class="logo" alt="شعار الشركة" />
        </div>

        <!-- العنوان الرئيسي -->
        <div class="main-title">
            عقد اتفاق {{ $record->title }}
        </div>

        <!-- صندوق رقم العقد والتاريخ -->
        <div class="meta-box">
            <table class="meta-table">
                <tr>
                    <td class="meta-right">
                        <span class="label-text">رقم العقد:</span>
                        <span class="value-text">CONTRACT-{{ $record->id }}</span>
                    </td>
                    <td class="meta-divider">|</td>
                    <td class="meta-left">
                        <span class="label-text">التاريخ:</span>
                        <span class="value-text">{{ now()->format('d/m/Y') }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- الخط الفاصل -->
        <div class="header-line"></div>
    </div>

    <!-- محتوى العقد -->
    <div class="contract-body">
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
</body>

</html>
