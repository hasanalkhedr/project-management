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
        Schema::create('employee_contracts', function (Blueprint $table) {
            $table->id();

            // معلومات الموظف
            $table->string('employee_name');
            $table->string('employee_id_number');
            $table->text('employee_address');
            $table->string('employee_phone');
            $table->string('employee_email')->nullable();

            // معلومات الوظيفة
            $table->string('job_title');
            $table->string('department')->nullable();
            $table->text('job_description')->nullable();

            // معلومات الشركة
            $table->string('company_name');
            $table->string('company_commercial_registration')->nullable();
            $table->text('company_address');
            $table->string('company_phone');

            // الراتب والمزايا
            $table->decimal('basic_salary', 15, 2);
            $table->foreignId('currency_id')->constrained();
            // $table->decimal('housing_allowance', 15, 2)->default(0);
            // $table->decimal('transportation_allowance', 15, 2)->default(0);
            // $table->decimal('other_allowances', 15, 2)->default(0);

            // // مدة العقد
            // $table->date('start_date');
            // $table->date('end_date')->nullable();
            // $table->integer('probation_period_days')->default(90);

            // // ساعات العمل
            // $table->string('working_hours')->nullable();
            // $table->string('working_days')->nullable();

            // حالة العقد
            // $table->enum('status', ['pending', 'active', 'completed', 'terminated', 'cancelled'])->default('pending');

            // التواريخ
            $table->date('contract_date');

            // محتوى العقد
            $table->text('preamble_content')->nullable();
            // $table->text('subject_content')->nullable();
            // $table->text('responsibilities_content')->nullable();
            // $table->text('working_hours_content')->nullable();
            // $table->text('salary_content')->nullable();
            // $table->text('benefits_content')->nullable();
            // $table->text('leave_content')->nullable();
            // $table->text('termination_content')->nullable();
            // $table->text('confidentiality_content')->nullable();
            // $table->text('general_terms_content')->nullable();

$table->text('job_desc')->nullable();
$table->text('con_dur')->nullable();
$table->text('test_dur')->nullable();
$table->text('start_date')->nullable();
$table->text('sal_con')->nullable();
$table->text('leave')->nullable();
$table->text('vacation')->nullable();
$table->text('overtime')->nullable();
$table->text('working_hours')->nullable();
$table->text('conditions')->nullable();
$table->text('renew')->nullable();
$table->text('system_notes')->nullable();
$table->text('no_copies')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_contracts');
    }
};
