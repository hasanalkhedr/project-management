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
        Schema::create('asset_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('asset_name');
            $table->enum('asset_type', ['tool', 'equipment', 'device', 'vehicle', 'other']);
            $table->text('asset_description')->nullable();
            $table->string('serial_number')->nullable();
            $table->date('assignment_date');
            $table->date('return_date')->nullable();
            $table->string('condition_on_assignment')->default('good');
            $table->string('condition_on_return')->nullable();
            $table->enum('status', ['assigned', 'returned', 'lost', 'damaged'])->default('assigned');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_assignments');
    }
};
