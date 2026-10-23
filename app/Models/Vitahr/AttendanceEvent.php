<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceEvent extends Model
{

    protected $connection = 'vitahr';
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'employee_device_id',
        'attendance_record_id',
        'device_identifier',
        'client_event_id',
        'action',
        'captured_at',
        'latitude',
        'longitude',
        'accuracy',
        'sync_status',
        'received_at',
        'processed_at',
        'failure_reason',
    ];

    protected function casts(): array
{
    return [
        'action' => \App\Enums\AttendanceEventAction::class,
        'sync_status' => \App\Enums\AttendanceEventSyncStatus::class,
        'captured_at' => 'datetime',
        'received_at' => 'datetime',
        'processed_at' => 'datetime',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'accuracy' => 'decimal:2',
    ];
}

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function employeeDevice(): BelongsTo
    {
        return $this->belongsTo(EmployeeDevice::class);
    }

    public function attendanceRecord(): BelongsTo
    {
        return $this->belongsTo(AttendanceRecord::class);
    }
}
