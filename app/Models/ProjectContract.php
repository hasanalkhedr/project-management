<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProjectContract extends Model
{
    protected $fillable = [
        'owner_name',
        'owner_id_number',
        'owner_address',
        'owner_phone',
        'contractor_name',
        'contractor_commercial_registration',
        'contractor_address',
        'contractor_phone',
        'project_location',
        //'contract_subject',
        'execution_period',
        //'start_date',
        //'end_date',
        'total_contract_value',
        'currency_id',
        'initial_payment_percentage',
        'concrete_stage_payment_percentage',
        'finishing_stage_payment_percentage',
        'final_payment_percentage',
        'delay_penalty_percentage',
        'max_penalty_percentage',
        //'warranty_period',
        'status',
        'arbitration_location',
        'contract_date',
        //'site_delivery_date',
        //'approved_drawings',
        //'technical_specifications',
        //'general_terms',
        //'notes',
    ];

    protected $casts = [
        //'start_date' => 'date',
        //'end_date' => 'date',
        'contract_date' => 'date',
        //'site_delivery_date' => 'datetime',
        //'approved_drawings' => 'array',
        //'technical_specifications' => 'array',
        'total_contract_value' => 'decimal:2',
        'initial_payment_percentage' => 'decimal:2',
        'concrete_stage_payment_percentage' => 'decimal:2',
        'finishing_stage_payment_percentage' => 'decimal:2',
        'final_payment_percentage' => 'decimal:2',
        'delay_penalty_percentage' => 'decimal:2',
        'max_penalty_percentage' => 'decimal:2',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

}
