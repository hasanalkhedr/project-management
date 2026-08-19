<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Recruitment extends Model
{
    protected $fillable = [
        'candidate_name',
        'candidate_email',
        'candidate_phone',
        'position',
        'department',
        'applied_date',
        'status',
        'interview_date',
        'interview_notes',
        'offer_salary',
        'offer_status',
        'hired_date',
        'employee_id',
        'notes',
    ];

    protected $casts = [
        'applied_date' => 'date',
        'interview_date' => 'date',
        'hired_date' => 'date',
        'offer_salary' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInterviewed($query)
    {
        return $query->where('status', 'interviewed');
    }

    public function scopeHired($query)
    {
        return $query->where('status', 'hired');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
