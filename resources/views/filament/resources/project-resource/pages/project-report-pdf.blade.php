<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>كشف حساب المشروع - {{ $project->name }}</title>
    <link rel="stylesheet" href="{{public_path('css/reports.css')}}">
    <style>
        @font-face {
            font-family: 'almarai';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path('fonts/Almarai-ExtraBold.ttf') }}') format('truetype');
        }
    </style>
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
            text-align: right;
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
            <div class="report-title">كشف حساب
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
        </div>

        <!-- صندوق رقم العقد والتاريخ -->
        <div class="meta-box">
            <table class="meta-table">
                <tr>
                    {{-- <td class="meta-right">
                        <span class="label-text">رقم العقد:</span>
                        <span class="value-text">CONTRACT-{{ $record->id }}</span>
                    </td>
                    <td class="meta-divider">|</td> --}}
                    <td class="meta-left">
                        <span class="label-text">التاريخ:</span>
                        <span class="value-text">{{ $report_date }}</span>
                    </td>
                </tr>
            </table>
        </div>

        <!-- الخط الفاصل -->
        <div class="header-line"></div>
    </div>



    <div class="client-name">العميل: السيد/ة {{ $project->client->name }}- العنوان: {{ $project->client->address }} -
        جوال: {{ $project->client->phone }}</div>
    <div class="project-name">المشروع: {{ $project->name }}</div>
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
                    <td style="color: {{ $transaction->type === __('Expense') ? '#ef4444' : '#10b981' }};">
                        {{ $transaction->type === __('Expense') ? 'نفقة' : 'دفعة' }}
                    </td>
                    <td>{{ $transaction->description }}</td>
                    <td>{{ $transaction->supplier }}</td>
                    {{-- <td>{{ $transaction->invoice_number }}</td> --}}
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
            الملخص
        </h3>

        {{-- @if ($currency_filter === __('All Currencies')) --}}
        <!-- Show multi-currency table -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">العملة</th>
                    @if ($report_type !== 'payments')
                        <th style="padding: 8px; border: 1px solid #ddd; text-align: center;">النفقات</th>
                    @endif
                    @if ($report_type !== 'expenses')
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
                                style="padding: 8px; border: 1px solid #ddd; text-align: center;
                    color: {{ $currencyData['profit'] >= 0 ? '#10b981' : '#ef4444' }};">
                                {{ number_format($currencyData['profit'], 2) }}
                            </td>
                        @endif
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
        {{-- <table class="footer-table">
            <tr>
                <td class="spacer-cell">
                    <!-- Empty cell for balance -->
                </td>
                <td class="footer-content-cell">
                    {{-- <p>https://alrayanrealestate.com/</p>
                    <p>&copy; {{ date('Y') }} Al-Rayan Real Estate. All rights reserved</p> --}}{{--
                </td>
                <td class="stamp-cell">
                    <div class="report-manager">مدير الشركة</div>
                </td>
            </tr>
            <tr>
                <td class="spacer-cell">
                    <!-- Empty cell for balance -->
                </td>
                <td class="footer-content-cell">
                    {{-- <p>https://alrayanrealestate.com/</p>
                    <p>&copy; {{ date('Y') }} Al-Rayan Real Estate. All rights reserved</p> --}}{{--
                </td>
                <td class="stamp-cell">
                    @if (file_exists($stamp))
                        <img src="{{ $stamp }}" class="stamp" alt="شعار الشركة">
                    @endif
                </td>
            </tr>
        </table> --}}
    </div>
</body>

</html>
