<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AttendanceRecord extends Model
{

    protected $connection = 'vitahr';
    use HasFactory;

    protected $fillable = [
        'employee_id',

        'check_in_at',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_accuracy',
        'check_in_device_id',
        'check_in_work_location_id',

        'check_out_at',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_accuracy',
        'check_out_device_id',
        'check_out_work_location_id',
    ];

    protected $casts = [
        'check_in_at' => 'datetime',
        'check_in_latitude' => 'decimal:7',
        'check_in_longitude' => 'decimal:7',
        'check_in_accuracy' => 'decimal:2',

        'check_out_at' => 'datetime',
        'check_out_latitude' => 'decimal:7',
        'check_out_longitude' => 'decimal:7',
        'check_out_accuracy' => 'decimal:2',
    ];

    /**
     * Employee associated with this attendance record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Device used for check-in.
     */
    public function checkInDevice(): BelongsTo
    {
        return $this->belongsTo(
            EmployeeDevice::class,
            'check_in_device_id'
        );
    }

    /**
     * Device used for check-out.
     */
    public function checkOutDevice(): BelongsTo
    {
        return $this->belongsTo(
            EmployeeDevice::class,
            'check_out_device_id'
        );
    }

    /**
     * Work location the check-in was validated against.
     */
    public function checkInWorkLocation(): BelongsTo
    {
        return $this->belongsTo(
            WorkLocation::class,
            'check_in_work_location_id'
        );
    }

    /**
     * Work location the check-out was validated against.
     */
    public function checkOutWorkLocation(): BelongsTo
    {
        return $this->belongsTo(
            WorkLocation::class,
            'check_out_work_location_id'
        );
    }

    public function attendanceEvents(): HasMany
{
    return $this->hasMany(AttendanceEvent::class);
}
}
