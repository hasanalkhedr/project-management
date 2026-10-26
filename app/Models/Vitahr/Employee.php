<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
<<<<<<< HEAD
use Illuminate\Database\Eloquent\Relations\HasOne;
=======
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0

class Employee extends Model
{

    protected $connection = 'vitahr';
    use HasFactory;

    protected $fillable = [
<<<<<<< HEAD
=======
        'user_id',
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0
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
<<<<<<< HEAD
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
=======
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
>>>>>>> 86eefc5d13472f7be23f475d108a730aa12de8b0
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
