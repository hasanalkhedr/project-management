<!DOCTYPE html>
<html
    lang="{{ app()->getLocale() }}"
    dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}"
>
<head>
    <meta charset="UTF-8">

    <title>
        {{ __('vitahr.attendance_report') }}
    </title>

    <style>
        @page {
            margin: 25px 20px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
            direction: {{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }};
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        h1 {
            font-size: 20px;
            margin: 0 0 8px;
        }

        .header {
            margin-bottom: 20px;
        }

        .info {
            width: 100%;
            margin-bottom: 15px;
        }

        .info td {
            padding: 3px 0;
        }

        .summary {
            width: 100%;
            margin-bottom: 20px;
        }

        .summary td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .summary-label {
            font-size: 9px;
            color: #666;
        }

        .summary-value {
            font-size: 16px;
            font-weight: bold;
            margin-top: 3px;
        }

        table.records {
            width: 100%;
            border-collapse: collapse;
        }

        table.records th {
            background: #eeeeee;
            font-weight: bold;
            padding: 6px 4px;
            border: 1px solid #ccc;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        table.records td {
            padding: 5px 4px;
            border: 1px solid #ddd;
            text-align: {{ app()->getLocale() === 'ar' ? 'right' : 'left' }};
        }

        .text-center {
            text-align: center !important;
        }

        .open {
            font-weight: bold;
        }

        .footer {
            margin-top: 15px;
            font-size: 8px;
            color: #777;
        }
    </style>
</head>

<body>

    <div class="header">

        <h1>
            {{ __('vitahr.attendance_report') }}
        </h1>

        <table class="info">

            <tr>

                <td>
                    <strong>{{ __('vitahr.from') }}:</strong>
                    {{ $from ?? '-' }}
                </td>

                <td>
                    <strong>{{ __('vitahr.until') }}:</strong>
                    {{ $until ?? '-' }}
                </td>

            </tr>

            <tr>

                <td colspan="2">
                    <strong>{{ __('vitahr.employees') }}:</strong>
                    {{ $employeeLabel }}
                </td>

            </tr>

        </table>

    </div>


    {{-- Summary --}}

    <table class="summary">

        <tr>

            <td>

                <div class="summary-label">
                    {{ __('vitahr.attendance_report_employees') }}
                </div>

                <div class="summary-value">
                    {{ $totalEmployees }}
                </div>

            </td>

            <td>

                <div class="summary-label">
                    {{ __('vitahr.attendance_records_summary') }}
                </div>

                <div class="summary-value">
                    {{ $totalRecords }}
                </div>

            </td>

            <td>

                <div class="summary-label">
                    {{ __('vitahr.completed') }}
                </div>

                <div class="summary-value">
                    {{ $totalCompleted }}
                </div>

            </td>

            <td>

                <div class="summary-label">
                    {{ __('vitahr.open') }}
                </div>

                <div class="summary-value">
                    {{ $totalOpen }}
                </div>

            </td>

        </tr>

    </table>


    {{-- Records --}}

    <table class="records">

        <thead>

            <tr>

                <th>
                    {{ __('vitahr.employee_no') }}
                </th>

                <th>
                    {{ __('vitahr.employee') }}
                </th>

                <th>
                    {{ __('vitahr.work_location') }}
                </th>

                <th>
                    {{ __('vitahr.date') }}
                </th>

                <th>
                    {{ __('vitahr.check_in') }}
                </th>

                <th>
                    {{ __('vitahr.check_out') }}
                </th>

                <th class="text-center">
                    {{ __('vitahr.duration') }}
                </th>

                <th class="text-center">
                    {{ __('vitahr.status') }}
                </th>

            </tr>

        </thead>

        <tbody>

            @forelse ($records as $record)

                @php

                    $duration = '-';

                    if (
                        $record->check_in_at &&
                        $record->check_out_at
                    ) {

                        $seconds =
                            $record->check_in_at
                                ->diffInSeconds(
                                    $record->check_out_at
                                );

                        $hours = intdiv(
                            $seconds,
                            3600
                        );

                        $minutes = intdiv(
                            $seconds % 3600,
                            60
                        );

                        $duration = sprintf(
                            '%02d:%02d',
                            $hours,
                            $minutes
                        );
                    }

                @endphp

                <tr>

                    <td>
                        {{ $record->employee?->employee_number ?? '-' }}
                    </td>

                    <td>
                        {{ $record->employee?->user?->name ?? '-' }}
                    </td>

                    <td>
                        {{ $record->checkInWorkLocation?->name ?? '-' }}
                    </td>

                    <td>
                        {{ $record->check_in_at?->format('Y-m-d') ?? '-' }}
                    </td>

                    <td>
                        {{ $record->check_in_at?->format('H:i:s') ?? '-' }}
                    </td>

                    <td>
                        {{ $record->check_out_at?->format('H:i:s') ?? '-' }}
                    </td>

                    <td class="text-center">
                        {{ $duration }}
                    </td>

                    <td class="text-center">

                        @if ($record->check_out_at)

                            {{ __('vitahr.completed') }}

                        @else

                            <span class="open">
                                {{ __('vitahr.open') }}
                            </span>

                        @endif

                    </td>

                </tr>

            @empty

                <tr>

                    <td
                        colspan="8"
                        class="text-center"
                    >
                        {{ __('vitahr.no_attendance_records') }}
                    </td>

                </tr>

            @endforelse

        </tbody>

    </table>


    <div class="footer">

        {{ __('vitahr.generated_on') }}

        {{ now()->format('Y-m-d H:i:s') }}

    </div>

</body>
</html>
