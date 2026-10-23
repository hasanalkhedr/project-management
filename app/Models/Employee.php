<?php

namespace App\Models;

use App\Models\Vitahr\AttendanceRecord;
use App\Models\Vitahr\Employee as VitahrEmployee;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * The employee record — lives on the shared vitahr database (the mobile
 * app's source of truth). The old local employees table/profile fields are
 * gone; what remains here are the relations into this app's own tables
 * (leaves, payrolls, contracts, …) which still key rows by employee_id.
 */
class Employee extends VitahrEmployee
{
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

    public function employeeContracts(): HasMany
    {
        return $this->hasMany(EmployeeContract::class);
    }

    public function employeeContractNews(): HasMany
    {
        return $this->hasMany(EmployeeContractNew::class);
    }

    public function recruitments(): HasMany
    {
        return $this->hasMany(Recruitment::class);
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'employee_id');
    }
}
