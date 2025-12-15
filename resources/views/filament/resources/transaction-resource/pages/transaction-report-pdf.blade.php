<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>كشف حساب الدفعات والنفقات الخاصة</title>
    <style>
        @font-face {
            font-family: 'almarai';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/Almarai-ExtraBold.ttf') }}') format('truetype');
        }

        body {
            font-family: 'almarai';
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
            width: 50%;
            vertical-align: middle;
            rowspan: 3;
        }

        .logo {
            max-height: 250px;
            width: auto;
            max-width: 300px;
            height: 250px;
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
            font-weight: bold;
            margin: 0;
        }

        .report-title {
            font-size: 28px;
            font-weight: bold;
            margin: 0;
        }

        .report-meta {
            font-size: 12px;
            font-weight: bold;
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
            width: 100%;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
        }

        .stamp-cell {
            width: 30%;
            vertical-align: bottom;
            text-align: left;
            margin-right: 40px;
        }

        .footer-content-cell {
            vertical-align: bottom;
            text-align: center;
            width: 50%;
            color: #666;
        }

        .spacer-cell {
            width: 20%;
        }

        .stamp {
            max-height: 160px;
            width: auto;
            max-width: 160px;
            height: 160px;
            object-fit: contain;
            vertical-align: bottom;
        }

    </style>
</head>
<body>
    <div class="header">
        <table class="header-table">
            <tr>
                {{-- <td class="content-cell" colspan="2">
                    <div class="company-title">شركة أبراج الريان للمقاولات</div>
                </td> --}}
                <td class="logo-cell" rowspan="3" colspan="2">
                    @if (file_exists(public_path('images/alrayan-logo2025.png')))
                        <img src="{{ $logo }}" class="logo" alt="شعار الشركة" />
                    @endif
                </td>
            </tr>
            <tr>
                <td class="content-cell">
                     <div class="report-title">كشف حساب
                        @if ($report_type === 'payments')
                            الدفعات الخاصة
                        @elseif($report_type === 'expenses')
                            النفقات الخاصة
                        @else
                            الدفعات والنفقات الخاصة
                        @endif
                        @if ($currency_filter === __('All Currencies'))
                            بكل العملات
                        @else
                            بال{{ $currency_filter }}
                        @endif
                    </div>
                </td>
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

    <!-- Summary Section -->
    <div class="summary-section">
        <h3 style="text-align: center; margin-bottom: 15px;">
            الملخص
        </h3>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">العملة</th>
                    @if ($report_type !== 'payment')
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">النفقات</th>
                    @endif
                    @if ($report_type !== 'expense')
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">الدفعات</th>
                    @endif
                    @if ($report_type === 'both')
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">الرصيد (+ربح/-خسارة)</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($by_currency as $currencyCode => $currencyData)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $currencyCode }}
                        </td>
                        @if ($report_type !== 'payment')
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: #ef4444;">
                                {{ number_format($currencyData['expenses'], 2) }}
                            </td>
                        @endif
                        @if ($report_type !== 'expense')
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: #10b981;">
                                {{ number_format($currencyData['payments'], 2) }}
                            </td>
                        @endif
                        @if ($report_type === 'both')
                            <td
                                style="padding: 8px; border: 1px solid #ddd; text-align: center;
                    color: {{ $currencyData['profit'] >= 0 ? '#10b981' : '#ef4444' }};">
                                {{ number_format($currencyData['profit'], 2) }}
                            </td>
                        @endif
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
                <th>النوع</th>
                <th>الوصف</th>
                <th>المورد/الطريقة</th>
                {{-- <th>الرقم المرجعي</th> --}}
                <th>المبلغ</th>
                <th>العملة</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $transaction)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('j F Y') }}</td>
                    <td style="color: {{ $transaction->type === 'expense' ? '#ef4444' : '#10b981' }};">
                        {{ $transaction->type === 'expense' ? 'نفقة' : 'دفعة' }}
                    </td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->supplier }}</td>
                    {{-- <td>{{ $transaction->invoice_number }}</td> --}}
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
        <table class="footer-table">
            <tr>
                <td class="spacer-cell">
                    <!-- Empty cell for balance -->
                </td>
                <td class="footer-content-cell">
                    <p>https://alrayanrealestate.com/</p>
                    <p>&copy; {{ date('Y') }} Al-Rayan Real Estate. All rights reserved</p>
                </td>
                <td class="stamp-cell">
                    @if (file_exists($stamp))
                        <img src="{{ $stamp }}" class="stamp" alt="شعار الشركة">
                    @endif
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
