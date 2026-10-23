<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeDevice extends Model
{

    protected $connection = 'vitahr';
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'device_identifier',
        'device_name',
        'platform',
        'is_active',
        'registered_at',
        'last_used_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'registered_at' => 'datetime',
        'last_used_at' => 'datetime',
    ];

    /**
     * Employee who owns this device.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Attendance records where this device was used for check-in.
     */
    public function checkInAttendanceRecords(): HasMany
    {
        return $this->hasMany(
            AttendanceRecord::class,
            'check_in_device_id'
        );
    }

    /**
     * Attendance records where this device was used for check-out.
     */
    public function checkOutAttendanceRecords(): HasMany
    {
        return $this->hasMany(
            AttendanceRecord::class,
            'check_out_device_id'
        );
    }

    public function attendanceEvents(): HasMany
{
    return $this->hasMany(AttendanceEvent::class);
}
}
