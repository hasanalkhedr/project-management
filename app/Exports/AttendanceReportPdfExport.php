<?php

namespace App\Exports;

use App\Models\Vitahr\AttendanceRecord;
use Illuminate\Database\Eloquent\Builder;

class AttendanceReportPdfExport
{
    public function __construct(
        protected array $employeeIds = [],
        protected ?string $from = null,
        protected ?string $until = null,
    ) {
    }

    public function query(): Builder
    {
        return AttendanceRecord::query()
            ->with([
                'employee.user',
                'checkInWorkLocation',
            ])
            ->when(
                ! empty($this->employeeIds),
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

    public function records()
    {
        return $this->query()->get();
    }
}
