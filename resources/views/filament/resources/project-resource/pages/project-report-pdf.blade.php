<!DOCTYPE html>
<html>
<head>
    <title>Project Report - {{ $project->name }}</title>
    <style>
        @page {
            margin: 35mm 10mm 20mm 10mm;
            header: html_header;
            footer: html_footer;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .logo {
            max-height: 20rem;
            margin-bottom: 10px;
        }

        .title-page {
            page-break-after: always;
            text-align: center;
            padding-top: 5mm;
        }

        .title-page h1 {
            font-size: 24px;
            margin-bottom: 10px;
        }

        .title-page .subtitle {
            font-size: 18px;
            color: #666;
            margin-bottom: 30px;
        }

        .title-page .report-info {
            margin-top: 30px;
            font-size: 14px;
            color: #666;
        }

        .period {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        .currency-section {
            page-break-inside: avoid;
            margin-bottom: 10px;
        }

        .currency-title {
            background-color: #f2f2f2;
            padding: 10px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .summary-container {
            display: table;
            width: 100%;
            margin-top: 10px;
            border-spacing: 0 10px;
        }

        .summary-card {
            display: table-cell;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            text-align: center;
        }

        .summary-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 18px;
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
    {{-- <!-- Header for all pages -->
    <htmlpageheader name="header">
        <table width="100%">
            <tr>
                <td style="width: 20%; text-align: left;">
                    @if(file_exists($logo))
                        <img src="{{ $logo }}" class="logo" alt="Company Logo">
                    @endif
                </td>
                <td style="width: 60%; text-align: center; vertical-align: middle;">
                    <h2 style="margin: 0;">{{ $project->name }}</h2>
                </td>
                <td style="width: 20%; text-align: right; vertical-align: bottom;">
                    <small>{{ $report_date }}</small>
                </td>
            </tr>
        </table>
        <hr style="border: 0.5px solid #ddd; margin: 5px 0 15px 0;">
    </htmlpageheader> --}}

    {{-- <!-- Footer for all pages -->
    <htmlpagefooter name="footer">
        <table width="100%">
            <tr>
                <td style="text-align: left; width: 33%;">
                    <small>Generated on: {{ $report_date }}</small>
                </td>
                <td style="text-align: center; width: 34%;">
                    <small>Page {PAGENO} of {nbpg}</small>
                </td>
                <td style="text-align: right; width: 33%;">
                    <small>&copy; {{ date('Y') }} Your Company Name</small>
                </td>
            </tr>
        </table>
    </htmlpagefooter> --}}

    <!-- Title Page (First Page) -->
    <div class="title-page">
        @if(file_exists($logo))
            <img src="{{ $logo }}" class="logo" alt="Company Logo">
        @endif

        <h1>Project Financial Report</h1>
        <div class="subtitle">{{ $project->name }}</div>

        @if ($start_date && $end_date)
            <div class="period">
                Period: {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}
            </div>
        @else
            <div class="period">Complete Project Report</div>
        @endif

        <div class="report-info">
            Report generated on: {{ $report_date }}
        </div>
    </div>

    <!-- Content Pages -->
    @foreach($by_currency as $currencyCode => $currencyData)
        <div class="currency-section">
            <div class="currency-title">{{ $currencyCode }} Transactions</div>

            @if(isset($expenses[$currencyCode]) && $expenses[$currencyCode]->count())
                <h3>Expenses ({{ $currencyCode }})</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Supplier</th>
                            <th>Invoice #</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses[$currencyCode] as $expense)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}</td>
                                <td>{{ $expense->description }}</td>
                                <td>{{ $expense->supplier }}</td>
                                <td>{{ $expense->invoice_number }}</td>
                                <td>{{ number_format($expense->amount, 2) }} {{ $currencyCode }}</td>
                            </tr>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td colspan="4">Total Expenses</td>
                            <td>{{ number_format($currencyData['expenses'], 2) }} {{ $currencyCode }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            @if(isset($payments[$currencyCode]) && $payments[$currencyCode]->count())
                <h3>Payments ({{ $currencyCode }})</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Payment Method</th>
                            <th>Reference #</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments[$currencyCode] as $payment)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }}</td>
                                <td>{{ $payment->description }}</td>
                                <td>{{ $payment->payment_method }}</td>
                                <td>{{ $payment->reference }}</td>
                                <td>{{ number_format($payment->amount, 2) }} {{ $currencyCode }}</td>
                            </tr>
                        @endforeach
                        <tr style="font-weight: bold;">
                            <td colspan="4">Total Payments</td>
                            <td>{{ number_format($currencyData['payments'], 2) }} {{ $currencyCode }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif

            <div class="summary-container">
                <div class="summary-card">
                    <div class="summary-title">Total Expenses ({{ $currencyCode }})</div>
                    <div class="summary-value">{{ number_format($currencyData['expenses'], 2) }} {{ $currencyCode }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-title">Total Payments ({{ $currencyCode }})</div>
                    <div class="summary-value">{{ number_format($currencyData['payments'], 2) }} {{ $currencyCode }}</div>
                </div>
                <div class="summary-card">
                    <div class="summary-title">Profit ({{ $currencyCode }})</div>
                    <div class="summary-value {{ $currencyData['profit'] >= 0 ? 'positive' : 'negative' }}">
                        {{ number_format($currencyData['profit'], 2) }} {{ $currencyCode }}
                    </div>
                </div>
            </div>
        </div>

        @if(!$loop->last)
            <div class="page-break"></div>
        @endif
    @endforeach

    {{-- <div class="page-break"></div>

    <div class="currency-section">
        <div class="currency-title">Overall Project Summary</div>
        <div class="summary-container">
            <div class="summary-card">
                <div class="summary-title">Total Expenses (All Currencies)</div>
                <div class="summary-value">{{ number_format($total_expenses, 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Total Payments (All Currencies)</div>
                <div class="summary-value">{{ number_format($total_payments, 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Net Profit (All Currencies)</div>
                <div class="summary-value {{ $total_profit >= 0 ? 'positive' : 'negative' }}">
                    {{ number_format($total_profit, 2) }}
                </div>
            </div>
        </div>
    </div> --}}
</body>
</html>
