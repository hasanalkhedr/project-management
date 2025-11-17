<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory;
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'exchange_rate',
        'is_default',
    ];

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
    public function profits(): HasMany
    {
        return $this->hasMany(Profit::class);
    }
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function project_contracts(): HasMany
    {
        return $this->hasMany(ProjectContract::class);
    }
}
