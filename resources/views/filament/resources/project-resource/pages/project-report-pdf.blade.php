<!DOCTYPE html>
<html>
<head>
    <title>Project Report - {{ $project->name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            display: table;
            width: 100%;
            margin-bottom: 20px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            width: 20%;
            vertical-align: top;
            padding-right: 20px;
        }

        .header-right {
            display: table-cell;
            width: 80%;
            vertical-align: middle;
        }

        .logo {
            max-height: 80px; /* Increased logo size */
            width: auto;
        }

        .report-title {
            font-size: 20px;
            margin: 0 0 5px 0;
            text-align: left;
        }

        .project-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            text-align: left;
        }

        .report-meta {
            font-size: 12px;
            color: #666;
            margin-bottom: 0;
            text-align: left;
        }

        .transactions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
            font-size: 12px;
        }

        .transactions-table th,
        .transactions-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
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
            color: #666;
            text-align: center;
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div class="header">
        <div class="header-left">
            @if(file_exists($logo))
                <img src="{{ $logo }}" class="logo" alt="Company Logo">
            @endif
        </div>
        <div class="header-right">
            <div class="report-title">Project Financial Report</div>
            <div class="project-name">{{ $project->name }}</div>

            @if ($start_date && $end_date)
                <div class="report-meta">
                    Period: {{ \Carbon\Carbon::parse($start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($end_date)->format('M d, Y') }}<br>
                    Report generated on: {{ $report_date }}
                </div>
            @else
                <div class="report-meta">
                    Complete Project Report<br>
                    Report generated on: {{ $report_date }}
                </div>
            @endif
        </div>
    </div>

    <!-- Transactions Table -->
    <table class="transactions-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Type</th>
                <th>Description</th>
                <th>Supplier/Method</th>
                <th>Reference #</th>
                <th>Amount</th>
                <th>Currency</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $currencyCode => $currencyExpenses)
                @foreach($currencyExpenses as $expense)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($expense->date)->format('M d, Y') }}</td>
                        <td style="color: #ef4444;">Expense</td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ $expense->supplier }}</td>
                        <td>{{ $expense->invoice_number }}</td>
                        <td style="color: #ef4444;">{{ number_format($expense->amount, 2) }}</td>
                        <td>{{ $currencyCode }}</td>
                    </tr>
                @endforeach
            @endforeach

            @foreach($payments as $currencyCode => $currencyPayments)
                @foreach($currencyPayments as $payment)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($payment->date)->format('M d, Y') }}</td>
                        <td style="color: #10b981;">Payment</td>
                        <td>{{ $payment->description }}</td>
                        <td>{{ $payment->payment_method }}</td>
                        <td>{{ $payment->reference }}</td>
                        <td style="color: #10b981;">{{ number_format($payment->amount, 2) }}</td>
                        <td>{{ $currencyCode }}</td>
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <!-- Summary Section -->
    <div class="summary-section">
        <table class="currency-summaries-table">
            <tr>
                @foreach($by_currency as $currencyCode => $currencyData)
                    <td class="currency-summary-cell">
                        <div class="currency-title">{{ $currencyCode }}</div>
                        <div class="currency-summary-row">
                            <div class="summary-card">
                                <div class="summary-title">Expenses</div>
                                <div class="summary-value">{{ number_format($currencyData['expenses'], 2) }}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-title">Payments</div>
                                <div class="summary-value">{{ number_format($currencyData['payments'], 2) }}</div>
                            </div>
                            <div class="summary-card">
                                <div class="summary-title">Profit</div>
                                <div class="summary-value {{ $currencyData['profit'] >= 0 ? 'positive' : 'negative' }}">
                                    {{ number_format($currencyData['profit'], 2) }}
                                </div>
                            </div>
                        </div>
                    </td>
                    <!-- Add a new row after every 3 currencies for better readability -->
                    @if($loop->iteration % 3 == 0 && !$loop->last)
                        </tr><tr>
                    @endif
                @endforeach
            </tr>
        </table>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <p>For any inquiries, please contact: support@yourcompany.com | Phone: +123 456 7890</p>
        <p>&copy; {{ date('Y') }} Your Company Name. All rights reserved.</p>
    </div>
</body>
</html>
