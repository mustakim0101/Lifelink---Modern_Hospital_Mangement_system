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
        Schema::create('nurse_vital_sign_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admission_id')->constrained('admissions')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id');
            $table->foreignId('nurse_id')->constrained('nurses', 'nurse_id');
            $table->timestamp('measured_at')->useCurrent();
            $table->decimal('temperature_c', 4, 1)->nullable();
            $table->unsignedSmallInteger('pulse_bpm')->nullable();
            $table->unsignedSmallInteger('systolic_bp')->nullable();
            $table->unsignedSmallInteger('diastolic_bp')->nullable();
            $table->unsignedSmallInteger('respiration_rate')->nullable();
            $table->unsignedTinyInteger('spo2_percent')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index(['admission_id', 'measured_at']);
            $table->index(['patient_id', 'measured_at']);
            $table->index(['nurse_id', 'measured_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurse_vital_sign_logs');
    }
};
