<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>كشف حساب المشروع - {{ $project->name }}</title>
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
            max-height: 120px;
            width: auto;
            max-width: 300px;
        }

        .header {
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            overflow: hidden;
        }

        .header-left {
            display: table-cell;
            width: 20%;
            vertical-align: top;
            padding-left: 20px;
        }

        .logo {
            height: 100px !important;
            width: auto !important;
            max-width: 200px !important;
        }

        .logo-container {
            float: left;
            /* Changed from right to left */
            width: 25%;
        }

        .header-content {
            float: right;
            /* Changed from left to right */
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

        .company-title {
            font-size: 20px;
            margin: 0 0 5px 0;
            text-align: right;
            color: #ebb436;
        }

        .report-title {
            font-size: 30px;
            margin: 0 0 5px 0;
            text-align: center;
        }

        .project-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: right;
        }
        .client-name {
            font-size: 18px;
            text-align: right;
            margin-right: 30px;
        }

        .report-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 0;
            text-align: right;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
            direction: rtl;
        }


        .transactions-table th,
        .transactions-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: right;
        }

        .transactions-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .summary-section {
            margin-bottom: 30px;
        }

        .currency-summaries-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 5px;
            margin-bottom: 20px;
            direction: rtl;
        }

        .currency-summary-cell {
            border: 1px solid #eee;
            border-radius: 5px;
            background-color: #f9f9f9;
            padding: 3px;
            vertical-align: top;
        }

        .currency-title {
            font-weight: bold;
            margin-bottom: 5px;
            text-align: center;
            padding-bottom: 3px;
            border-bottom: 1px solid #ddd;
        }

        .currency-summary-row {
            display: table;
            width: 100%;
        }

        .summary-card {
            display: table-cell;
            width: 33%;
            padding: 5px;
            border-radius: 4px;
            background-color: white;
            border: 1px solid #e0e0e0;
            text-align: center;
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
    </style>
</head>

<body>
    <!-- Header Section -->
    <div class="header">
        <div class="logo-container">
            @if (file_exists($logo))
                <img src="{{ $logo }}" class="logo" style="height: 150px; width: auto; object-fit: contain;"
                    alt="شعار الشركة">
            @endif
        </div>
        <div class="header-content">
            <div class="company-title">شركة الريان للمقاولات</div>
            <div class="report-title">كشف حساب
                @if ($currency_filter === __('All Currencies'))
                    بكل العملات
                @else
                    بال{{ $currency_filter }}
                @endif
            </div>
            <div class="project-name">{{ $project->name }}</div>
            @if ($start_date && $end_date)
            <div class="report-meta">
                الفترة: {{ \Carbon\Carbon::parse($start_date)->translatedFormat('j F Y') }} إلى
                {{ \Carbon\Carbon::parse($end_date)->translatedFormat('j F Y') }}<br>
                تم إنشاء التقرير في: {{ $report_date }}
            </div>
            @endif
        </div>
    </div>
    <div class="client-name">العميل: السيد/ة {{ $project->client->name }}: {{$project->client->address}} {{ $project->client->phone }}</div>
    <!-- Transactions Table -->
    <table class="transactions-table">
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>النوع</th>
                <th>الوصف</th>
                <th>المورد/الطريقة</th>
                <th>الرقم المرجعي</th>
                <th>المبلغ</th>
                <th>العملة</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('j F Y') }}</td>
                    <td style="color: {{ $transaction->type === __('Expense') ? '#ef4444' : '#10b981' }};">
                        {{ $transaction->type === __('Expense') ? 'مصروف' : 'دفع' }}
                    </td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->supplier }}</td>
                    <td>{{ $transaction->invoice_number }}</td>
                    <td style="color: {{ $transaction->type === __('Expense') ? '#ef4444' : '#10b981' }};">
                        {{ number_format($transaction->amount, 2) }}
                    </td>
                    <td>{{ $transaction->currency->code }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <h3 style="text-align: center; margin-bottom: 15px;">
            @if ($currency_filter === __('All Currencies'))
                ملخص حسب العملة
            @else
                ملخص لعملة {{ $currency_filter }}
            @endif
        </h3>

        {{-- @if ($currency_filter === __('All Currencies')) --}}
        <!-- Show multi-currency table -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">العملة</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المصروفات</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المدفوعات</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">الرصيد (+ربح/-خسارة)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($by_currency as $currencyCode => $currencyData)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $currencyCode }}
                        </td>
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
        {{-- @else
            <!-- Show single currency summary -->
            <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 20px;">
                <div style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <div style="font-size: 12px; color: #666;">المصروفات</div>
                    <div style="font-size: 18px; font-weight: bold; color: #ef4444;">
                        {{ number_format($by_currency[$currency_filter]['expenses'], 2) }} {{ $currency_filter }}
                    </div>
                </div>
                <div style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <div style="font-size: 12px; color: #666;">المدفوعات</div>
                    <div style="font-size: 18px; font-weight: bold; color: #10b981;">
                        {{ number_format($by_currency[$currency_filter]['payments'], 2) }} {{ $currency_filter }}
                    </div>
                </div>
                <div style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                    <div style="font-size: 12px; color: #666;">الرصيد (+ربح/-خسارة)</div>
                    <div
                        style="font-size: 18px; font-weight: bold;
                    color: {{ $by_currency[$currency_filter]['profit'] >= 0 ? '#10b981' : '#ef4444' }};">
                        {{ number_format($by_currency[$currency_filter]['profit'], 2) }} {{ $currency_filter }}
                    </div>
                </div>
            </div>
        @endif --}}
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <p>https://alrayanrealestate.com/ | Mobile: </p>
        <p>&copy; {{ date('Y') }} Al-Rayan Real Estate. All rights reserved</p>
    </div>
</body>

</html>
