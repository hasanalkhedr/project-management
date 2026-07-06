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
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->string('company_registration_date')->nullable()->after('company_commercial_registration');
            $table->string('company_registration_source')->nullable()->after('company_registration_date');
            $table->string('company_general_manager_name')->nullable()->after('company_registration_source');
            $table->string('company_representative_name')->nullable()->after('company_general_manager_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->dropColumn([
                'company_registration_date',
                'company_registration_source',
                'company_general_manager_name',
                'company_representative_name',
            ]);
        });
    }
};
