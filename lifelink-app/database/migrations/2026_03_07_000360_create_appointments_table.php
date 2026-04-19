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
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('doctor_user_id')->nullable()->constrained('users');
            $table->timestamp('appointment_datetime');
            $table->string('status', 20)->default('Booked');
            $table->foreignId('cancelled_by_user_id')->nullable()->constrained('users');
            $table->string('cancel_reason', 255)->nullable();
            $table->timestamps();

            $table->index(['department_id', 'status']);
            $table->index('patient_id');
            $table->index('appointment_datetime');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};

