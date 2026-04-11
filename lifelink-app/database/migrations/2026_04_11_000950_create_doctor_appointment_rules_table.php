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
        Schema::create('doctor_appointment_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_user_id')->constrained('doctors', 'doctor_id')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments');
            $table->unsignedTinyInteger('day_of_week');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedInteger('daily_capacity');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['doctor_user_id', 'day_of_week', 'is_active'], 'idx_doc_appt_rules_doctor_day_active');
            $table->index(['department_id', 'day_of_week', 'is_active'], 'idx_doc_appt_rules_dept_day_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_appointment_rules');
    }
};

