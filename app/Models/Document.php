<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    protected $fillable = [
        'employee_id',
        'document_type',
        'document_name',
        'file_path',
        'issue_date',
        'expiry_date',
        'expiry_alert_days',
        'status',
        'notes',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'expiry_alert_days' => 'integer',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        $alertDays = $this->expiry_alert_days ?? 30;
        $expiryDate = \Carbon\Carbon::parse($this->expiry_date);
        $today = \Carbon\Carbon::now();

        return $today->diffInDays($expiryDate, false) <= $alertDays;
    }

    public function isExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return \Carbon\Carbon::parse($this->expiry_date)->isPast();
    }

    public function scopeExpiringSoon($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<=', now()->addDays(30))
                     ->where('expiry_date', '>', now());
    }

    public function scopeExpired($query)
    {
        return $query->whereNotNull('expiry_date')
                     ->where('expiry_date', '<', now());
    }
}
