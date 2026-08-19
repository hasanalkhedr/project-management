<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'basic_salary',
        'housing_allowance',
        'transportation_allowance',
        'other_allowances',
        'overtime_pay',
        'bonuses',
        'deductions',
        'advances',
        'gross_salary',
        'net_salary',
        'payment_date',
        'payment_method',
        'notes',
    ];

    protected $casts = [
        'month' => 'integer',
        'year' => 'integer',
        'basic_salary' => 'decimal:2',
        'housing_allowance' => 'decimal:2',
        'transportation_allowance' => 'decimal:2',
        'other_allowances' => 'decimal:2',
        'overtime_pay' => 'decimal:2',
        'bonuses' => 'decimal:2',
        'deductions' => 'decimal:2',
        'advances' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function calculateGrossSalary(): void
    {
        $this->gross_salary = $this->basic_salary + $this->housing_allowance +
                              $this->transportation_allowance + $this->other_allowances +
                              $this->overtime_pay + $this->bonuses;
    }

    public function calculateNetSalary(): void
    {
        $this->net_salary = $this->gross_salary - $this->deductions - $this->advances;
    }

    public function calculateAll(): void
    {
        $this->calculateGrossSalary();
        $this->calculateNetSalary();
    }
}
