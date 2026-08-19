<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('employee_contract_news', function (Blueprint $table) {
            // Company information
            $table->string('company_name')->nullable();
            $table->string('company_commercial_registration')->nullable();
            $table->date('company_registration_date')->nullable();
            $table->string('company_registration_source')->nullable();
            $table->string('company_general_manager_name')->nullable();
            $table->string('company_representative_name')->nullable();
            $table->text('company_address')->nullable();
            $table->string('company_phone')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_contract_news', function (Blueprint $table) {
            $table->dropColumn([
                'company_name',
                'company_commercial_registration',
                'company_registration_date',
                'company_registration_source',
                'company_general_manager_name',
                'company_representative_name',
                'company_address',
                'company_phone',
            ]);
        });
    }
};
