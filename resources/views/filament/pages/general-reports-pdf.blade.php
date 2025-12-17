<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>كشف حساب عام - {{ $report_date }}</title>
    <link rel="stylesheet" href="{{public_path('css/reports.css')}}">
    <style>
        @font-face {
            font-family: 'almarai';
            font-style: normal;
            font-weight: normal;
            src: url('{{ storage_path('fonts/Almarai-ExtraBold.ttf') }}') format('truetype');
        }
    </style>
</head>

<body class="general-reports">
    <!-- Header Section -->
    <div class="header">
        <table class="header-table">
            <tr>
                {{-- <td class="content-cell" colspan="2">
                    <div class="company-title">شركة أبراج الريان للمقاولات</div>
                </td> --}}
                <td class="logo-cell" rowspan="2">
                    @if (file_exists(public_path('images/alrayan-logo2025.png')))
                        <img src="{{ $logo }}" class="logo" alt="شعار الشركة" />
                    @endif
                </td>
                <td class="content-cell"></td>
            </tr>

            <tr>
                <td class="content-cell">
                    <div class="report-title">كشف حساب عام
                        @if ($report_type === 'payments')
                            الدفعات
                        @elseif($report_type === 'expenses')
                            النفقات
                        @else
                            الدفعات والنفقات
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
        <h3 style="text-align: center; margin-bottom: 15px;">ملخص حسب العملة</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">العملة</th>
                    @if ($report_type !== 'payments')
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المصروفات</th>
                    @endif
                    @if ($report_type !== 'expenses')
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المدفوعات</th>
                    @endif
                    @if ($report_type === 'both')
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">الربح</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($currencySummaries as $currencyCode => $currencyData)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $currencyCode }}</td>
                        @if ($report_type !== 'payments')
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: #ef4444;">
                                {{ number_format($currencyData['expenses'], 2) }}
                            </td>
                        @endif
                        @if ($report_type !== 'expenses')
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: #10b981;">
                                {{ number_format($currencyData['payments'], 2) }}
                            </td>
                        @endif
                        @if ($report_type === 'both')
                            <td
                                style="padding: 8px; border: 1px solid #ddd; text-align: center; color: {{ $currencyData['profit'] >= 0 ? '#10b981' : '#ef4444' }};">
                                {{ number_format($currencyData['profit'], 2) }}
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Projects Summary Section -->
    <div class="summary-section">
        <h3 style="text-align: center; margin-bottom: 15px;">أداء المشاريع</h3>
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المشروع</th>
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">العملة</th>
                    @if ($report_type !== 'payments')
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المصروفات</th>
                    @endif
                    @if ($report_type !== 'expenses')
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">المدفوعات</th>
                    @endif
                    @if ($report_type === 'both')
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">الرصيد</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @foreach ($projectSummaries as $project)
                    @foreach ($project['currencies'] as $currency => $currencyData)
                        <tr>
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">
                                {{ $project['name'] }}</td>
                            <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $currency }}
                            </td>
                            @if ($report_type !== 'payments')
                                <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: #ef4444;">
                                    {{ number_format($currencyData['expenses'], 2) }}
                                </td>
                            @endif
                            @if ($report_type !== 'expenses')
                                <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: #10b981;">
                                    {{ number_format($currencyData['payments'], 2) }}
                                </td>
                            @endif
                            @if ($report_type === 'both')
                                <td
                                    style="padding: 8px; border: 1px solid #ddd; text-align: center; color: {{ $currencyData['profit'] >= 0 ? '#10b981' : '#ef4444' }};">
                                    {{ number_format($currencyData['profit'], 2) }}
                                </td>
                            @endif
                        </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Transactions Table -->
    <div class="summary-section">
        <h3 style="text-align: center; margin-bottom: 15px;">سجل العمليات</h3>
        <table class="transactions-table">
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>النوع</th>
                    <th>المشروع</th>
                    <th>الوصف</th>
                    <th>المبلغ</th>
                    <th>العملة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transactions as $transaction)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($transaction['date'])->translatedFormat('j F Y') }}</td>
                        <td style="color: {{ $transaction['type'] === __('Expense') ? '#ef4444' : '#10b981' }};">
                            {{ $transaction['type'] === __('Expense') ? 'نفقة' : 'دفعة' }}
                        </td>
                        <td>{{ $transaction['project'] }}</td>
                        <td>{{ $transaction['description'] }}</td>
                        <td style="color: {{ $transaction['type'] === __('Expense') ? '#ef4444' : '#10b981' }};">
                            {{ number_format($transaction['amount'], 2) }}
                        </td>
                        <td>{{ $transaction['currency'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

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
