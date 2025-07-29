<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profit extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_id',
        'currency_id',
        'amount',
        'description',
        'date',
        'payment_method',
        'reference',
    ];

    protected $casts = [
        'date' => 'date'
    ];
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
