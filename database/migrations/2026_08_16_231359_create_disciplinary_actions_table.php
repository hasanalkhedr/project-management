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
        Schema::create('disciplinary_actions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('action_date');
            $table->enum('action_type', ['verbal_warning', 'written_warning', 'penalty', 'suspension', 'termination']);
            $table->string('reason');
            $table->text('description')->nullable();
            $table->foreignId('issued_by')->constrained('employees');
            $table->decimal('penalty_amount', 10, 2)->default(0);
            $table->integer('suspension_days')->default(0);
            $table->integer('warning_level')->default(1);
            $table->enum('status', ['active', 'resolved', 'appealed'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disciplinary_actions');
    }
};
