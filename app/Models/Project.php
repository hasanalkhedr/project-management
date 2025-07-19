<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'client_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
    ];
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class)->with('currency');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class)->with('currency');
    }

    public function getProgressPercentage(): float
    {
        if ($this->status === 'completed')
            return 100;
        if ($this->status === 'planned')
            return 0;
        if (!$this->start_date || !$this->end_date)
            return 0;

        $totalDays = $this->start_date->diffInDays($this->end_date);
        $daysPassed = $this->start_date->diffInDays(now());

        // Ensure we return a float value between 0 and 100
        return min(($daysPassed / $totalDays) * 100, 100);
    }

    public function getProgressColor(): string
    {
        return match (true) {
            $this->status === 'completed' => 'success',
            $this->getProgressPercentage() >= 70 => 'primary',
            $this->getProgressPercentage() >= 40 => 'warning',
            default => 'danger',
        };
    }

    public function isOnTrack(): bool
    {
        if (!$this->end_date || $this->status !== 'in_progress')
            return true;

        $expectedProgress = $this->start_date->diffInDays(now()) / $this->start_date->diffInDays($this->end_date);
        $actualProgress = $this->getProgressPercentage() / 100;

        return $actualProgress >= $expectedProgress;
    }
    public function getTotalExpensesAttribute()
    {
        return $this->expenses->sum('amount');
    }

    public function getTotalPaymentsAttribute()
    {
        return $this->payments->sum('amount');
    }

    public function getNetProfitAttribute()
    {
        return $this->total_payments - $this->total_expenses;
    }
    // app/Models/Project.php
    protected $appends = ['total_expenses', 'total_payments', 'net_profit'];

    public function getProgressPercentageAttribute()
    {
        if ($this->status === 'completed')
            return 100;
        if ($this->status === 'planned')
            return 0;
        if (!$this->start_date || !$this->end_date)
            return 0;

        $totalDays = $this->start_date->diffInDays($this->end_date);
        $daysPassed = $this->start_date->diffInDays(now());

        return min(($daysPassed / $totalDays) * 100, 100);
    }
}
