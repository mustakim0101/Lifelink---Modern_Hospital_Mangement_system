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
        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments');
            $table->string('diagnosis', 255);
            $table->string('care_level_requested', 20);
            $table->string('care_level_assigned', 20)->nullable();
            $table->string('status', 20)->default('Admitted');
            $table->timestamp('admit_date')->useCurrent();
            $table->timestamp('discharge_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['department_id', 'status']);
            $table->index('patient_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admissions');
    }
};

