<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>كشف حساب دفعات الإشراف</title>
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

            margin: 0 0;
            page-break-inside: avoid;
            direction: rtl;
            unicode-bidi: embed;
        }

        th,
        td {
            padding: 8px;
            text-align: right;

            page-break-inside: avoid;
        }

        .header {
            border-bottom: 1px solid #ddd;
        }

        .header-table {
            width: 100%;
        }

        .logo-cell {
            width: 20%;
            vertical-align: middle;
            rowspan: 3;
        }

        .logo {
            max-height: 150px;
            width: auto;
            max-width: 200px;
            height: 120px;
            object-fit: contain;
        }

        .title-cell {
            width: 60%;
            text-align: center;
            vertical-align: top;
        }

        .content-cell {
            vertical-align: bottom;
            text-align: right;
        }

        .company-title {
            font-size: 22px;
            margin: 0;
        }

        .report-title {
            font-size: 28px;
            margin: 0;
        }

        .report-meta {
            font-size: 12px;
            color: #666;
            margin: 0;
        }

        .project-name {
            font-size: 18px;
            text-align: right;
            margin-right: 30px;
            margin-bottom: 10px;
        }

        .client-name {
            font-size: 18px;
            text-align: right;
            margin-right: 30px;
            margin-top: 15px;
        }

        /* Transactions Table (optimized) */
        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 14px;
            /* Slightly reduced from 12px */
            direction: rtl;
            page-break-inside: auto;
        }

        .transactions-table th,
        .transactions-table td {
            padding: 6px;
            /* Reduced from 8px */
            text-align: right;
            border: 1px solid #ddd;
            page-break-inside: avoid;
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

        .footer {
            font-size: 10px;
            text-align: center;
            margin-top: 20px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td class="content-cell" colspan="2">
                    @if (file_exists($company_name))
                        <img src="{{ $company_name }}" class="company-title" alt="شعار الشركة">
                    @endif
                    {{-- <div class="company-title">شركة الريان للمقاولات</div> --}}
                </td>
                <td class="logo-cell" rowspan="6">
                    @if (file_exists($logo))
                        <img src="{{ $logo }}" class="logo" alt="شعار الشركة">
                    @endif
                </td>
            </tr>
            <tr>
                <td class="content-cell"></td>
            </tr>
             <tr>
                <td class="content-cell"></td>
            </tr>
            <tr>
                <!-- Empty middle cell (title spans all rows) -->
                <td class="content-cell">
                    <!-- Empty space in middle row -->
                </td>
                <td class="title-cell" rowspan="2">
                    <div class="report-title">كشف حساب دفعات الإشراف

                        @if ($currency_filter === __('All Currencies'))
                            بكل العملات
                        @else
                            بال{{ $currency_filter }}
                        @endif
                    </div>
                </td>

            </tr>
            <tr>
                <td class="content-cell"></td>
            </tr>
            <tr>
                <td class="content-cell">
                    @if ($start_date && $end_date)
                        <div class="report-meta">
                            تاريخ: {{ $report_date }}
                        </div>
                    @endif
                </td>
            </tr>
        </table>
    </div>
@if($project_filter !== __('All Projects'))
    <div class="client-name">العميل: السيد/ة {{ $project_filter->client->name }}- العنوان: {{ $project_filter->client->address }} -
        جوال: {{ $project_filter->client->phone }}</div>
    <div class="project-name">المشروع: {{ $project_filter->name }}</div>
@endif

    {{-- <!-- Summary Section -->
    <div class="summary-section">
        <h3 style="text-align: center; margin-bottom: 15px;">
            الملخص
        </h3>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">العملة</th>
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المجموع</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($by_currency as $currencyCode => $currencyData)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $currencyCode }}
                        </td>

                            <td
                                style="padding: 8px; border: 1px solid #ddd; text-align: center;
                    color: {{ $currencyData >= 0 ? '#10b981' : '#ef4444' }};">
                                {{ number_format($currencyData, 2) }}
                            </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div> --}}
    <!-- Summary Section -->
<div class="summary-section">
    <h3 style="text-align: center; margin-bottom: 15px;">
        الملخص
    </h3>

    <!-- Currency Summary Table -->
    <h4 style="text-align: right; margin: 15px 0 5px 0;">حسب العملة</h4>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">العملة</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المجموع</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($by_currency as $currencyCode => $amount)
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $currencyCode }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: {{ $amount >= 0 ? '#10b981' : '#ef4444' }};">
                        {{ number_format($amount, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Project Summary Table -->
    <h4 style="text-align: right; margin: 15px 0 5px 0;">حسب المشروع</h4>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #f2f2f2;">
                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المشروع</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">العملة</th>
                <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المجموع</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($by_project as $projectName => $projectData)
                <tr>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $projectName }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $projectData['currency'] }}</td>
                    <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: {{ $projectData['total'] >= 0 ? '#10b981' : '#ef4444' }};">
                        {{ number_format($projectData['total'], 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

    <!-- Transactions Table -->
    <table class="transactions-table">
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>المشروع</th>
                <th>الوصف</th>
                <th>المورد/الطريقة</th>
                <th>الرقم المرجعي</th>
                <th>المبلغ</th>
                <th>العملة</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($profits as $transaction)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('j F Y') }}</td>
                    <td>
                        {{ $transaction->project->name }}
                    </td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->supplier }}</td>
                    <td>{{ $transaction->invoice_number }}</td>
                    <td style="color: {{ $transaction->type === 'expense' ? '#ef4444' : '#10b981' }};">
                        {{ number_format($transaction->amount, 2) }}
                    </td>
                    <td>{{ $transaction->currency->code }}</td>
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
