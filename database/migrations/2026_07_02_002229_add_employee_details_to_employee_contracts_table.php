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
            $table->string('employee_nationality')->nullable()->after('employee_name');
            $table->string('employee_id_issue_date')->nullable()->after('employee_id_number');
            $table->string('employee_id_issue_place')->nullable()->after('employee_id_issue_date');
            $table->string('employee_id_issue_number')->nullable()->after('employee_id_issue_place');
            $table->string('employee_permanent_address')->nullable()->after('employee_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->dropColumn([
                'employee_nationality',
                'employee_id_issue_date',
                'employee_id_issue_place',
                'employee_id_issue_number',
                'employee_permanent_address',
            ]);
        });
    }
};
