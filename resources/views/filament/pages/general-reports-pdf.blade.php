<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>تقرير عام - {{ $report_date }}</title>
    <style>
        @font-face {
            font-family: 'amiri';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/Amiri-Regular.ttf') }}') format('truetype');
        }

        body {
            font-family: 'amiri', sans-serif;
            line-height: 1.5;
            word-spacing: 3px;
            direction: rtl;
            text-align: right;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            page-break-inside: avoid;
            direction: rtl;
            unicode-bidi: embed;
        }

        th,
        td {
            padding: 8px;
            text-align: right;
            border: 1px solid #ddd;
            page-break-inside: avoid;
        }

        .logo {
            max-height: 80px;
            width: auto;
            float: right;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
            overflow: hidden;
        }

        .logo-container {
            float: left;
            width: 20%;
        }

        .header-content {
            float: right;
            width: 75%;
            text-align: right;
        }

        .footer {
            font-size: 10px;
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            color: #666;
        }

        .report-title {
            font-size: 20px;
            margin: 0 0 5px 0;
            text-align: right;
        }

        .report-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 0;
            text-align: right;
        }

        .summary-card {
            border: 1px solid #eee;
            border-radius: 5px;
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 15px;
        }

        .currency-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
            padding-bottom: 3px;
            border-bottom: 1px solid #ddd;
        }

        .summary-title {
            font-size: 10px;
            margin-bottom: 3px;
            color: #666;
        }

        .summary-value {
            font-size: 12px;
            font-weight: bold;
        }

        .positive {
            color: #10b981;
        }

        .negative {
            color: #ef4444;
        }

        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <div class="logo-container">
            @if (file_exists($logo))
                <img src="{{ $logo }}" class="logo" alt="شعار الشركة">
            @endif
        </div>
        <div class="header-content">
            <div class="report-title">تقرير مالي عام</div>
            @if ($start_date && $end_date)
                <div class="report-meta">
                    الفترة: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('j F Y') }} إلى
                    {{ \Carbon\Carbon::parse($end_date)->translatedFormat('j F Y') }}<br>
                    تم إنشاء التقرير في: {{ $report_date }}
                </div>
            @endif
        </div>
    </div>


    <!-- Summary Section -->
    <div class="summary-section">
        <h3 style="text-align: center; margin-bottom: 15px;">ملخص حسب العملة</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">العملة</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المصروفات</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المدفوعات</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">الربح</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($currencySummaries as $currencyCode => $currencyData)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $currencyCode }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: #ef4444;">
                            {{ number_format($currencyData['expenses'], 2) }}
                        </td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: #10b981;">
                            {{ number_format($currencyData['payments'], 2) }}
                        </td>
                        <td
                            style="padding: 8px; border: 1px solid #ddd; text-align: center;
                    color: {{ $currencyData['profit'] >= 0 ? '#10b981' : '#ef4444' }};">
                            {{ number_format($currencyData['profit'], 2) }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <!-- Projects Summary Section -->
    <h3 style="text-align: center; margin-bottom: 15px;">أداء المشاريع</h3>
    <table>
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th>المشروع</th>
                @foreach (array_keys($currencySummaries) as $currency)
                    <th colspan="3" style="text-align: center;">{{ $currency }}</th>
                @endforeach
            </tr>
            <tr>
                <th></th>
                @foreach (array_keys($currencySummaries) as $currency)
                    <th style="text-align: center;">المصروفات</th>
                    <th style="text-align: center;">المدفوعات</th>
                    <th style="text-align: center;">الربح</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($projectSummaries as $project)
                <tr>
                    <td>{{ $project['name'] }}</td>
                    @foreach (array_keys($currencySummaries) as $currency)
                        @php
                            $currencyData = $project['currencies'][$currency] ?? [
                                'expenses' => 0,
                                'payments' => 0,
                                'profit' => 0,
                            ];
                        @endphp
                        <td style="text-align: center; color: #ef4444;">
                            {{ number_format($currencyData['expenses'], 2) }}
                        </td>
                        <td style="text-align: center; color: #10b981;">
                            {{ number_format($currencyData['payments'], 2) }}
                        </td>
                        <td
                            style="text-align: center; color: {{ $currencyData['profit'] >= 0 ? '#10b981' : '#ef4444' }};">
                            {{ number_format($currencyData['profit'], 2) }}
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Footer Section -->
    <div class="footer">
        <p>https://alrayanrealestate.com/ | Mobile: </p>
        <p>&copy; {{ date('Y') }} Al-Rayan Real Estate. All rights reserved</p>
    </div>
</body>

</html>
