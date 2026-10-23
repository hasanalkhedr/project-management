<?php

namespace App\Exports;

use App\Models\Vitahr\AttendanceRecord;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

class AttendanceReportExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    ShouldAutoSize,
    WithEvents
{
    public function __construct(
        protected array $employeeIds = [],
        protected ?string $from = null,
        protected ?string $until = null,
    ) {
    }

    public function registerEvents(): array
{
    return [
        AfterSheet::class => function (AfterSheet $event): void {
            $event->sheet
                ->getDelegate()
                ->setRightToLeft(true);
        },
    ];
}
    public function query(): Builder
    {
        return AttendanceRecord::query()
            ->with([
                'employee.user',
                'checkInWorkLocation',
            ])
            ->when(
                !empty($this->employeeIds),
                fn (Builder $query) =>
                $query->whereIn(
                    'employee_id',
                    $this->employeeIds
                )
            )
            ->when(
                $this->from,
                fn (Builder $query, $date) =>
                $query->whereDate(
                    'check_in_at',
                    '>=',
                    $date
                )
            )
            ->when(
                $this->until,
                fn (Builder $query, $date) =>
                $query->whereDate(
                    'check_in_at',
                    '<=',
                    $date
                )
            )
            ->orderByDesc('check_in_at');
    }

    public function headings(): array
    {
        return [
            __('vitahr.attendance_export_employee_number'),
            __('vitahr.attendance_export_employee'),
            __('vitahr.attendance_export_date'),
            __('vitahr.attendance_export_work_location'),
            __('vitahr.attendance_export_check_in'),
            __('vitahr.attendance_export_check_out'),
            __('vitahr.attendance_export_duration'),
            __('vitahr.attendance_export_status'),
            __('vitahr.attendance_export_check_in_latitude'),
            __('vitahr.attendance_export_check_in_longitude'),
            __('vitahr.attendance_export_check_in_accuracy'),
            __('vitahr.attendance_export_check_out_latitude'),
            __('vitahr.attendance_export_check_out_longitude'),
            __('vitahr.attendance_export_check_out_accuracy'),
        ];
    }

    public function map($record): array
    {
        $duration = '-';

        if (
            $record->check_in_at
            && $record->check_out_at
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

        return [
            $record->employee?->employee_number ?? '-',

            $record->employee?->user?->name ?? '-',

            $record->check_in_at?->format(
                'Y-m-d'
            ) ?? '-',

            $record->checkInWorkLocation?->name ?? '-',

            $record->check_in_at?->format(
                'H:i:s'
            ) ?? '-',

            $record->check_out_at?->format(
                'H:i:s'
            ) ?? '-',

            $duration,

            $record->check_out_at
                ? __('vitahr.completed')
                : __('vitahr.open'),

            $record->check_in_latitude,

            $record->check_in_longitude,

            $record->check_in_accuracy,

            $record->check_out_latitude,

            $record->check_out_longitude,

            $record->check_out_accuracy,
        ];
    }
}
