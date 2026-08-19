<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceEvaluation extends Model
{
    protected $fillable = [
        'employee_id',
        'evaluation_date',
        'evaluated_by',
        'period_start',
        'period_end',
        'overall_score',
        'strengths',
        'weaknesses',
        'recommendations',
        'decision',
        'notes',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'period_start' => 'date',
        'period_end' => 'date',
        'overall_score' => 'decimal:2',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'evaluated_by');
    }
}
