<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>كشف حساب مورّد</title>
    <link rel="stylesheet" href="{{public_path('css/reports.css')}}">
    <style>
        @font-face {
            font-family: 'almarai';
            font-style: normal;
            font-weight: bold;
            src: url('{{ storage_path('fonts/Almarai-ExtraBold.ttf') }}') format('truetype');
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
                <td class="logo-cell" rowspan="2" colspan="2">
                    @if (file_exists(public_path('images/alrayan-logo2025.png')))
                        <img src="{{ $logo }}" class="logo" alt="شعار الشركة" />
                    @endif
                </td>
                <td class="content-cell"></td>
            </tr>
            <tr>
                <td class="content-cell">
                     <div class="report-title">
                        {{$report_title}}
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
    <div class="client-name">المورد: السيد/ة {{ $supplier_name }}- العنوان: {{ $supplier_address }} -
        جوال: {{ $supplier_phone }}</div>

@if($project_filter !== __('All Projects'))
    <div class="project-name">المشروع: {{ $project_filter->name }}</div>
@endif


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
                @foreach ($projectData as $currencyCode => $total)
                    <tr>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $projectName }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center;">{{ $currencyCode }}</td>
                        <td style="padding: 8px; border: 1px solid #ddd; text-align: center; color: {{ $total >= 0 ? '#10b981' : '#ef4444' }};">
                            {{ number_format($total, 2) }}
                        </td>
                    </tr>
                @endforeach
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
                {{-- <th>الرقم المرجعي</th> --}}
                <th>المبلغ</th>
                <th>العملة</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $transaction)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($transaction->date)->translatedFormat('j F Y') }}</td>
                    <td>
                        {{ $transaction->project->name }}
                    </td>
                    <td>{{ $transaction->description }}</td>
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
