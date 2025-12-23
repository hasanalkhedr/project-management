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
        Schema::table('project_contracts', function (Blueprint $table) {
            $table->longText('preamble_content')->nullable()->comment('مقدمة العقد');
            $table->longText('subject_content')->nullable()->comment('موضوع العقد');
            $table->longText('specifications_content')->nullable()->comment('المواصفات والمخططات');
            $table->longText('duration_content')->nullable()->comment('مدة التنفيذ');
            $table->longText('payment_content')->nullable()->comment('القيمة وطريقة الدفع');
            $table->longText('obligations_content')->nullable()->comment('الالتزامات');
            $table->longText('warranty_content')->nullable()->comment('الضمان والصيانة');
            $table->longText('termination_content')->nullable()->comment('فسخ العقد');
            $table->longText('arbitration_content')->nullable()->comment('التحكيم');
            $table->longText('general_terms_content')->nullable()->comment('أحكام عامة');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('project_contracts', function (Blueprint $table) {
            $table->dropColumn([
                'preamble_content',
                'subject_content',
                'specifications_content',
                'duration_content',
                'payment_content',
                'obligations_content',
                'warranty_content',
                'termination_content',
                'arbitration_content',
                'general_terms_content',
            ]);
        });
    }
};
