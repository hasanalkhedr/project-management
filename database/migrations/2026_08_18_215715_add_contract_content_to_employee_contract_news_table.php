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
            $table->longText('job_desc')->nullable();
            $table->longText('con_dur')->nullable();
            $table->longText('test_dur')->nullable();
            $table->longText('sal_con')->nullable();
            $table->longText('leave')->nullable();
            $table->longText('vacation')->nullable();
            $table->longText('overtime')->nullable();
            $table->longText('conditions')->nullable();
            $table->longText('renew')->nullable();
            $table->longText('system_notes')->nullable();
            $table->integer('no_copies')->default(2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_contract_news', function (Blueprint $table) {
            $table->dropColumn(['job_desc', 'con_dur', 'test_dur', 'sal_con', 'leave', 'vacation', 'overtime', 'conditions', 'renew', 'system_notes', 'no_copies']);
        });
    }
};
