<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class WorkLocation extends Model
{

    protected $connection = 'vitahr';
    use HasFactory;

    protected $fillable = [
        'name',
        'latitude',
        'longitude',
        'radius',
        'work_start_time',
        'work_end_time',
        'timezone',
        'is_active',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'radius' => 'integer',
        // 'work_start_time' => 'datetime:H:i',
        // 'work_end_time' => 'datetime:H:i',
        'work_start_time' => 'string',
        'work_end_time' => 'string',
        'is_active' => 'boolean',
    ];

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'employee_work_location')
            ->withTimestamps();
    }
}
