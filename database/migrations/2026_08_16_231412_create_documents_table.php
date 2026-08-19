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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->enum('document_type', ['national_id', 'contract', 'educational_certificate', 'photo', 'passport', 'driving_license', 'social_security', 'other']);
            $table->string('document_name');
            $table->string('file_path');
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->integer('expiry_alert_days')->default(30);
            $table->enum('status', ['active', 'expired', 'revoked'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
