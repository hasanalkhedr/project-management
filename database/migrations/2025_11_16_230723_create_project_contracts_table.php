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
        Schema::create('project_contracts', function (Blueprint $table) {
            $table->id();

            // معلومات الطرف الأول (المالك)
            $table->string('owner_name');
            $table->string('owner_id_number');
            $table->text('owner_address');
            $table->string('owner_phone');

            // معلومات الطرف الثاني (المقاول)
            $table->string('contractor_name');
            $table->string('contractor_commercial_registration');
            $table->text('contractor_address');
            $table->string('contractor_phone');

            // معلومات المشروع
            $table->text('project_location');
            //$table->text('contract_subject');

            // مدة التنفيذ
            $table->integer('execution_period'); // بالأيام
            //$table->date('start_date');
            //$table->date('end_date');

            // قيمة العقد وطريقة الدفع
            $table->decimal('total_contract_value', 15, 2);
            $table->foreignId('currency_id')->constrained();

            // نسب الدفعات
            $table->decimal('initial_payment_percentage', 5, 2);
            $table->decimal('concrete_stage_payment_percentage', 5, 2);
            $table->decimal('finishing_stage_payment_percentage', 5, 2);
            $table->decimal('final_payment_percentage', 5, 2);

            // غرامات التأخير
            $table->decimal('delay_penalty_percentage', 5, 2);
            $table->decimal('max_penalty_percentage', 5, 2);

            // الضمان
            //$table->integer('warranty_period')->default(12); // بالأشهر

            // حالة العقد
            $table->enum('status', ['pending', 'active', 'completed', 'terminated', 'cancelled'])->default('pending');

            // التحكيم وحل النزاعات
            $table->string('arbitration_location');

            // التواريخ
            $table->date('contract_date');
            //$table->timestamp('site_delivery_date')->nullable();

            // الملفات والمستندات
            //$table->json('approved_drawings')->nullable();
            //$table->json('technical_specifications')->nullable();

            //$table->text('general_terms')->nullable();
            //$table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_contracts');
    }
};
