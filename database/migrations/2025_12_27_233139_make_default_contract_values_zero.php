<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('project_contracts', function (Blueprint $table) {
            $table->decimal('total_contract_value', 15, 2)->default(0)->change();
            $table->foreignId('currency_id')->nullable()->change();
            $table->decimal('initial_payment_percentage', 5, 2)->default(0)->change();
            $table->decimal('concrete_stage_payment_percentage', 5, 2)->default(0)->change();
            $table->decimal('finishing_stage_payment_percentage', 5, 2)->default(0)->change();
            $table->decimal('final_payment_percentage', 5, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_contracts', function (Blueprint $table) {
            $table->decimal('total_contract_value', 15, 2)->change();
            $table->foreignId('currency_id')->change();
            $table->decimal('initial_payment_percentage', 5, 2)->change();
            $table->decimal('concrete_stage_payment_percentage', 5, 2)->change();
            $table->decimal('finishing_stage_payment_percentage', 5, 2)->change();
            $table->decimal('final_payment_percentage', 5, 2)->change();
        });
    }
};
