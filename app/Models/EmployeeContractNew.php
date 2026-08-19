<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeContractNew extends Model
{
    protected $fillable = [
        'employee_id',
        'start_date',
        'end_date',
        'contract_type',
        'probation_period_days',
        'working_hours',
        'working_days',
        'notes',
        'status',
        'job_desc',
        'con_dur',
        'test_dur',
        'sal_con',
        'leave',
        'vacation',
        'overtime',
        'conditions',
        'renew',
        'system_notes',
        'no_copies',
        'company_name',
        'company_commercial_registration',
        'company_registration_date',
        'company_registration_source',
        'company_general_manager_name',
        'company_representative_name',
        'company_address',
        'company_phone',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'company_registration_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
