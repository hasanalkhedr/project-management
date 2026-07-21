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
            $table->decimal('basic_salary_usd', 15, 2)->nullable()->after('basic_salary');
            $table->foreignId('currency_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_contracts', function (Blueprint $table) {
            $table->dropColumn('basic_salary_usd');
            $table->foreignId('currency_id')->constrained()->change();
        });
    }
};
