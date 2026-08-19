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
            $table->dropColumn([
                'basic_salary',
                'housing_allowance',
                'transportation_allowance',
                'other_allowances',
                'payment_method',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_contract_news', function (Blueprint $table) {
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('housing_allowance', 10, 2)->default(0);
            $table->decimal('transportation_allowance', 10, 2)->default(0);
            $table->decimal('other_allowances', 10, 2)->default(0);
            $table->string('payment_method')->nullable();
        });
    }
};
