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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            // Employee Number (Auto-generated)
            $table->string('employee_number')->unique();

            // Personal Information
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('nationality');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->enum('marital_status', ['single', 'married', 'divorced', 'widowed']);
            $table->string('national_id')->unique();
            $table->string('place_of_registration')->nullable();
            $table->string('photo')->nullable();

            // Contact Information
            $table->string('phone');
            $table->string('email')->nullable();
            $table->text('current_address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();

            // Work Information
            $table->string('department');
            $table->string('job_title');
            $table->foreignId('direct_manager_id')->nullable()->constrained('employees');
            $table->foreignId('project_id')->nullable()->constrained('projects');
            $table->enum('contract_type', ['full_time', 'part_time', 'daily', 'contractor']);
            $table->enum('employment_status', ['active', 'suspended', 'terminated']);
            $table->date('hire_date');
            $table->integer('probation_period')->default(90); // in days
            $table->date('contract_start_date')->nullable();
            $table->date('contract_end_date')->nullable();

            // Salary Information
            $table->decimal('basic_salary', 10, 2)->default(0);
            $table->decimal('housing_allowance', 10, 2)->default(0);
            $table->decimal('transportation_allowance', 10, 2)->default(0);
            $table->decimal('other_allowances', 10, 2)->default(0);
            $table->decimal('total_salary', 10, 2)->default(0);
            $table->enum('payment_method', ['cash', 'bank_transfer']);
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('social_security_number')->nullable();

            // Additional
            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
