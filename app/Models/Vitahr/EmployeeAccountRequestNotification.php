<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAccountRequestNotification extends Model
{

    protected $connection = 'vitahr';
    protected $fillable = [
        'employee_account_request_id',
        'type',
        'channel',
        'status',
        'fcm_token',
        'payload',
        'error_message',
        'sent_at',
    ];

    protected $hidden = ['fcm_token'];
    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'sent_at' => 'datetime',
        ];
    }

    public function employeeAccountRequest(): BelongsTo
    {
        return $this->belongsTo(
            EmployeeAccountRequest::class,
            'employee_account_request_id'
        );
    }
}
