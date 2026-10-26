<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($employee) {
            //dd($employee);
            //if (empty($employee->employee_number)) {
                $lastEmployee = Employee::orderBy('id', 'desc')->first();
                $nextId = $lastEmployee ? $lastEmployee->id + 1 : 1;
                $employee->employee_number = 'EMP-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);
            //}
        });
    }

    protected $fillable = [
        // Employee Number (Auto-generated)
        'employee_number',

        // Personal Information
        'name_ar',
        'name_en',
        'nationality',
        'date_of_birth',
        'gender',
        'marital_status',
        'national_id',
        'place_of_registration',
        'photo',

        // Contact Information
        'phone',
        'email',
        'current_address',
        'emergency_contact_name',
        'emergency_contact_phone',

        // Work Information
        'department_id',
        'job_title_id',
        'direct_manager_id',
        'project_id',
        'contract_type',
        'employment_status',
        'hire_date',
        'probation_period',
        'contract_start_date',
        'contract_end_date',

        // Salary Information
        'basic_salary',
        'housing_allowance',
        'transportation_allowance',
        'other_allowances',
        'total_salary',
        'payment_method',
        'bank_account_number',
        'bank_name',
        'social_security_number',

        // Additional
        'notes',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'contract_start_date' => 'date',
        'contract_end_date' => 'date',
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transportation_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'total_salary' => 'decimal:2',
    ];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves(): HasMany
    {
        return $this->hasMany(Leave::class);
    }

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function performanceEvaluations(): HasMany
    {
        return $this->hasMany(PerformanceEvaluation::class);
    }

    public function disciplinaryActions(): HasMany
    {
        return $this->hasMany(DisciplinaryAction::class);
    }

    public function assetAssignments(): HasMany
    {
        return $this->hasMany(AssetAssignment::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function directManager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'direct_manager_id');
    }

    public function subordinates(): HasMany
    {
        return $this->hasMany(Employee::class, 'direct_manager_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }

    // public function employeeContracts(): HasMany
    // {
    //     return $this->hasMany(EmployeeContract::class);
    // }

    public function contracts(): HasMany
    {
        return $this->hasMany(EmployeeContractNew::class);
    }

    public function getFullNameAttribute(): string
    {
        return $this->name_ar ?? $this->name_en;
    }

    public function getEmployeeNumberAttribute(): string
    {
        return 'EMP-' . str_pad($this->id, 5, '0', STR_PAD_LEFT);
    }

    public function calculateTotalSalary(): void
    {
        $this->total_salary = $this->basic_salary + $this->housing_allowance + $this->transportation_allowance + $this->other_allowances;
        $this->save();
    }
}
