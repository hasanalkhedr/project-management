<?php

namespace App\Models\Vitahr;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeAccountRequest extends Model
{

    protected $connection = 'vitahr';
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'employee_id',
        'email',
        'active_email',
        'password_hash',
        'fcm_token',
        'fcm_token_update_secret_hash',
        'status',
        'reviewed_by',
        'reviewed_at',
        'rejection_reason',
    ];


    protected $hidden = [
        'password_hash',
        'fcm_token',
        'fcm_token_update_secret_hash',
    ];


    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'reviewed_by'
        );
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(
            EmployeeAccountRequestNotification::class,
            'employee_account_request_id'
        );
    }
}
