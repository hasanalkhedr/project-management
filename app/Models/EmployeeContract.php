<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeContract extends Model
{
    protected $fillable = [
        'employee_name',
        'employee_nationality',
        'employee_id_number',
        'employee_id_issue_date',
        'employee_id_issue_place',
        'employee_id_issue_number',
        'employee_address',
        'employee_permanent_address',
        'employee_phone',
        'employee_email',
        'job_title',
        'department',
        'job_description',
        'company_name',
        'company_commercial_registration',
        'company_registration_date',
        'company_registration_source',
        'company_general_manager_name',
        'company_representative_name',
        'company_address',
        'company_phone',
        'basic_salary',
        'currency_id',


        'housing_allowance',
        // 'transportation_allowance',
        // 'other_allowances',
        // 'start_date',
        // 'end_date',
        // 'probation_period_days',
        // 'working_hours',
        // 'working_days',
        // 'status',
        'contract_date',

        'preamble_content',
        // 'subject_content',
        // 'responsibilities_content',
        // 'working_hours_content',
        // 'salary_content',
        // 'benefits_content',
        // 'leave_content',
        // 'termination_content',
        // 'confidentiality_content',
        // 'general_terms_content',

        'job_desc',
        'con_dur',
        'test_dur',
        'start_date',
        'sal_con',
        'leave',
        'vacation',
        'overtime',
        'working_hours',
        'conditions',
        'renew',
        'system_notes',
        'no_copies',
    ];

    protected $casts = [
        // 'start_date' => 'date',
        // 'end_date' => 'date',
        'contract_date' => 'date',
        'basic_salary' => 'decimal:2',
        // 'housing_allowance' => 'decimal:2',
        // 'transportation_allowance' => 'decimal:2',
        // 'other_allowances' => 'decimal:2',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    // Helper method to calculate total salary
    public function getTotalSalaryAttribute()
    {
        return $this->basic_salary + $this->housing_allowance + $this->transportation_allowance + $this->other_allowances;
    }

    // Helper method to format monetary values
    public function getFormattedTotalSalaryAttribute()
    {
        return number_format($this->total_salary, 2);
    }

    // Helper method to get contract number
    public function getContractNumberAttribute()
    {
        return 'EMP-CONTRACT-' . $this->id;
    }
}
