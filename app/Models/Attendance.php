<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'check_in_time',
        'check_out_time',
        'working_hours',
        'overtime_hours',
        'late_minutes',
        'is_absent',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'working_hours' => 'decimal:2',
        'overtime_hours' => 'decimal:2',
        'late_minutes' => 'integer',
        'is_absent' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function calculateWorkingHours(): void
    {
        if ($this->check_in_time && $this->check_out_time) {
            $this->working_hours = $this->check_out_time->diffInHours($this->check_in_time);

            // Calculate overtime (more than 8 hours)
            if ($this->working_hours > 8) {
                $this->overtime_hours = $this->working_hours - 8;
            } else {
                $this->overtime_hours = 0;
            }
        }
    }

    public function calculateLateMinutes($expectedStartTime = '09:00'): void
    {
        if ($this->check_in_time) {
            $expected = \Carbon\Carbon::parse($expectedStartTime);
            $actual = $this->check_in_time;

            if ($actual->gt($expected)) {
                $this->late_minutes = $expected->diffInMinutes($actual);
            } else {
                $this->late_minutes = 0;
            }
        }
    }
}
