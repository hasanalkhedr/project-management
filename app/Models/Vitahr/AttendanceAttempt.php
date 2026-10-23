<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceAttempt extends Model
{

    protected $connection = 'vitahr';
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'attendance_event_id',
        'action',
        'latitude',
        'longitude',
        'accuracy',
        'device_identifier',
        'result',
        'failure_reason',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'accuracy' => 'decimal:2',
    ];

    /**
     * Employee who made this attendance attempt.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Offline attendance event that produced this attempt.
     *
     * Online attempts have no attendance event.
     */
    public function attendanceEvent(): BelongsTo
    {
        return $this->belongsTo(AttendanceEvent::class);
    }
}
