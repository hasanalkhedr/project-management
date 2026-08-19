<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DisciplinaryAction extends Model
{
    protected $fillable = [
        'employee_id',
        'action_date',
        'action_type',
        'reason',
        'description',
        'issued_by',
        'penalty_amount',
        'suspension_days',
        'warning_level',
        'status',
        'notes',
    ];

    protected $casts = [
        'action_date' => 'date',
        'penalty_amount' => 'decimal:2',
        'suspension_days' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'issued_by');
    }
}
