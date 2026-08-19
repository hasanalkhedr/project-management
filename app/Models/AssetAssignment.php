<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetAssignment extends Model
{
    protected $fillable = [
        'employee_id',
        'asset_name',
        'asset_type',
        'asset_description',
        'serial_number',
        'assignment_date',
        'return_date',
        'condition_on_assignment',
        'condition_on_return',
        'status',
        'notes',
    ];

    protected $casts = [
        'assignment_date' => 'date',
        'return_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'assigned');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }
}
