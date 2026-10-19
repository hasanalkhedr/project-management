<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{

    protected $connection = 'vitahr';
    use HasFactory;

    protected $fillable = [
        'user_id',
        'emp_name',
        'employee_number',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Authentication user associated with this employee.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Work locations assigned to the employee.
     */
    public function workLocations(): BelongsToMany
    {
        return $this->belongsToMany(WorkLocation::class, 'employee_work_location')
            ->withTimestamps();
    }

    /**
     * Devices registered for this employee.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(EmployeeDevice::class);
    }

    /**
     * Attendance records of this employee.
     */
    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /**
     * Attendance attempts of this employee.
     */
    public function attendanceAttempts(): HasMany
    {
        return $this->hasMany(AttendanceAttempt::class);
    }

    public function attendanceEvents(): HasMany
{
    return $this->hasMany(AttendanceEvent::class);
}
}
