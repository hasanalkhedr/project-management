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
        Schema::create('performance_evaluations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('evaluation_date');
            $table->foreignId('evaluated_by')->constrained('employees');
            $table->date('period_start');
            $table->date('period_end');
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->text('strengths')->nullable();
            $table->text('weaknesses')->nullable();
            $table->text('recommendations')->nullable();
            $table->enum('decision', ['promote', 'retain', 'warning', 'terminate'])->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('performance_evaluations');
    }
};
