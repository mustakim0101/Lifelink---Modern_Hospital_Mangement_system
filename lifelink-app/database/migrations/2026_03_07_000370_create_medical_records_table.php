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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->cascadeOnDelete();
            $table->foreignId('admission_id')->nullable()->constrained('admissions');
            $table->foreignId('created_by_user_id')->constrained('users');
            $table->timestamp('record_datetime')->useCurrent();
            $table->string('diagnosis', 255);
            $table->text('treatment_plan');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'record_datetime']);
            $table->index('admission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};

