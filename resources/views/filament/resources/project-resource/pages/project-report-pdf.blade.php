<!DOCTYPE html>
<html>

<head>
    <title>Project Report - {{ $project->name }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .period {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }

        /* Table container - updated for PDF compatibility */
    .table-container {
        width: 100%;
        display: table;
        border-spacing: 20px 0; /* Horizontal gap only */
        margin-bottom: 30px;
    }

    .table-wrapper {
        display: table-cell;
        width: 50%; /* Each table takes half width */
        vertical-align: top; /* Align content to top */
        padding: 0;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
    }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .page-break {
            page-break-after: always;
        }

        .summary {
            margin-top: 30px;
        }

        /* Updated summary container for PDF compatibility */
        .summary-container {
            display: table;
            width: 100%;
            margin-top: 20px;
            border-spacing: 20px 0; /* Horizontal gap only */
        }

        .summary-card {
            display: table-cell;
            width: 33.33%; /* Force equal width for 3 cards */
            padding: 16px;
            border-radius: 8px;
            background: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            border: 1px solid #ddd;
            vertical-align: top; /* Align content to top */
        }

        /* Title styling */
        .summary-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 8px;
            font-weight: bold;
        }

        /* Value styling */
        .summary-value {
            font-size: 24px;
            font-weight: bold;
        }

        /* Profit/Loss colors */
        .profit-positive {
            color: #10b981;
        }

        .profit-negative {
            color: #ef4444;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Project Report: {{ $project->name }}</h1>
        <div class="period">
            @if ($start_date && $end_date)
                Period: {{ $start_date }} to {{ $end_date }}
            @else
                Complete Project Report
            @endif
        </div>
    </div>

    <div class="table-container">
        <div class="table-wrapper">
            <h2>Expenses</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Currency</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($expenses as $expense)
                        <tr>
                            <td>{{ $expense->date }}</td>
                            <td>{{ $expense->description }}</td>
                            <td>{{ number_format($expense->amount, 2) }}</td>
                            <td>{{ $expense->currency->code }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold;">
                        <td colspan="2">Total Expenses</td>
                        <td>{{ number_format($total_expenses, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-wrapper">
            <h2>Payments</h2>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                        <th>Currency</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($payments as $payment)
                        <tr>
                            <td>{{ $payment->date }}</td>
                            <td>{{ $payment->description }}</td>
                            <td>{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ $payment->currency->code }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold;">
                        <td colspan="2">Total Payments</td>
                        <td>{{ number_format($total_payments, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="summary">
        <h2>Project Summary</h2>
        <div class="summary-container">
            <div class="summary-card">
                <div class="summary-title">Total Expenses</div>
                <div class="summary-value">{{ number_format($total_expenses, 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Total Payments</div>
                <div class="summary-value">{{ number_format($total_payments, 2) }}</div>
            </div>
            <div class="summary-card">
                <div class="summary-title">Net Profit</div>
                <div class="summary-value {{ $profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    {{ number_format($profit, 2) }}
                </div>
            </div>
        </div>
    </div>
</body>

</html>
