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
        Schema::create('recruitments', function (Blueprint $table) {
            $table->id();
            $table->string('candidate_name');
            $table->string('candidate_email')->nullable();
            $table->string('candidate_phone')->nullable();
            $table->string('position');
            $table->string('department')->nullable();
            $table->date('applied_date');
            $table->enum('status', ['pending', 'reviewed', 'interviewed', 'offered', 'hired', 'rejected'])->default('pending');
            $table->date('interview_date')->nullable();
            $table->text('interview_notes')->nullable();
            $table->decimal('offer_salary', 10, 2)->nullable();
            $table->enum('offer_status', ['pending', 'accepted', 'rejected'])->nullable();
            $table->date('hired_date')->nullable();
            $table->foreignId('employee_id')->nullable()->constrained('employees');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recruitments');
    }
};
