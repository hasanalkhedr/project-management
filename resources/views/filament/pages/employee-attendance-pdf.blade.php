<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ public_path('css/reports.css') }}">
    <title>تقرير دوام الموظف</title>
    <style>
        body {
            font-family: 'Almarai', sans-serif;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header img {
            max-height: 80px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 20px 0;
            color: #1a365d;
        }
        .employee-info {
            background-color: #f7fafc;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .employee-info p {
            margin: 5px 0;
            font-size: 14px;
        }
        .employee-info strong {
            color: #2d3748;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            font-size: 12px;
        }
        th {
            background-color: #1a365d;
            color: white;
            font-weight: bold;
        }
        .present {
            background-color: #d4edda; /* أخضر فاتح للحضور */
        }
        .absent {
            background-color: #f8d7da; /* أحمر فاتح للغياب */
        }
        .leave {
            background-color: #cce5ff; /* أزرق فاتح للإجازات */
        }
        .weekend {
            background-color: #e2e3e5; /* رمادي للعطل الأسبوعية */
        }
        .not-recorded {
            background-color: #fff3cd; /* أصفر فاتح لغير مسجل */
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        .summary p {
            margin: 5px 0;
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
            text-align: center;
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
<body>
    <div class="header-container">
        <!-- الشعار أعلى اليسار -->
        <div class="logo-cell">
            <img src="{{ $logo }}" class="logo" alt="شعار الشركة" />
        </div>

        <!-- العنوان الرئيسي -->
        <div class="main-title">
    تقرير دوام الموظف</div>

    <div class="employee-info">
        <p><strong>اسم الموظف:</strong> {{ $employee->name_ar }} ({{ $employee->name_en }})</p>
        <p><strong>القسم:</strong> {{ $employee->department->name_ar ?? 'غير محدد' }}</p>
        <p><strong>المسمى الوظيفي:</strong> {{ $employee->jobTitle->name_ar ?? 'غير محدد' }}</p>
        <p><strong>تاريخ التقرير:</strong> {{ now()->format('d/m/Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>اليوم</th>
                <th></th>وقت الحضور</th>
                <th>وقت الانصراف</th>
                <th>ساعات العمل</th>
                <th>العمل الإضافي</th>
                <th>التأخير (دقيقة)</th>
                <th>الحالة</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dates as $date)
                @php
                    $dateStri = $date->format('Y-m-d H:i:s');
                    $attendance = $attendances->get($dateStri);
                    // Check if this date is within any leave period
                    $isOnLeave = false;
                    $leaveType = '';
                    foreach($leaves as $leave) {
                        if ($date->between(
                            \Carbon\Carbon::parse($leave->start_date),
                            \Carbon\Carbon::parse($leave->end_date)
                        )) {
                            $isOnLeave = true;
                            $leaveType = match($leave->leave_type) {
                                'annual' => 'إجازة سنوية',
                                'sick' => 'إجازة مرضية',
                                'emergency' => 'إجازة طارئة',
                                'unpaid' => 'بدون راتب',
                                default => $leave->leave_type,
                            };
                            break;
                        }
                    }

                    // Check if it's Friday (weekend)
                    $isWeekend = $date->dayOfWeek === 5;

                    // Determine status
                    if ($isWeekend) {
                        $status = 'عطلة أسبوعية';
                        $rowClass = 'weekend';
                    } elseif ($isOnLeave) {
                        $status = 'إجازة (' . $leaveType . ')';
                        $rowClass = 'leave';
                    } elseif ($attendance) {
                        $status = $attendance->is_absent ? 'غائب' : 'حاضر';
                        $rowClass = $attendance->is_absent ? 'absent' : 'present';
                    } else {
                        $status = 'غير مسجل';
                        $rowClass = 'not-recorded';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ $date->format('d/m/Y') }}</td>
                    <td>{{ match($date->dayOfWeek) {
                        0 => 'الأحد',
                        1 => 'الاثنين',
                        2 => 'الثلاثاء',
                        3 => 'الأربعاء',
                        4 => 'الخميس',
                        5 => 'الجمعة',
                        6 => 'السبت',
                    } }}</td>
                    <td>{{ $attendance && $attendance->check_in_time ? \Carbon\Carbon::parse($attendance->check_in_time)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance && $attendance->check_out_time ? \Carbon\Carbon::parse($attendance->check_out_time)->format('H:i') : '-' }}</td>
                    <td>{{ $attendance ? number_format($attendance->working_hours, 2) : '-' }}</td>
                    <td>{{ $attendance ? number_format($attendance->overtime_hours, 2) : '-' }}</td>
                    <td>{{ $attendance ? $attendance->late_minutes : '-' }}</td>
                    <td>{{ $status }}</td>
                    <td>{{ $attendance && $attendance->notes ? $attendance->notes : '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p><strong>إجمالي الأيام في الفترة:</strong> {{ count($dates) }}</p>
        <p><strong>أيام الحضور:</strong> {{ $attendances->where('is_absent', false)->count() }}</p>
        <p><strong>أيام الغياب:</strong> {{ $attendances->where('is_absent', true)->count() }}</p>
        <p><strong>أيام العطل الأسبوعية:</strong> {{ collect($dates)->filter(fn($d) => $d->dayOfWeek === 5)->count() }}</p>
        <p><strong>أيام الإجازات:</strong> {{ $leaves->sum('total_days') }}</p>
        <p><strong>إجمالي ساعات العمل:</strong> {{ number_format($attendances->sum('working_hours'), 2) }}</p>
        <p><strong>إجمالي العمل الإضافي:</strong> {{ number_format($attendances->sum('overtime_hours'), 2) }}</p>
        <p><strong>إجمالي دقائق التأخير:</strong> {{ $attendances->sum('late_minutes') }}</p>
    </div>
</body>
</html>
