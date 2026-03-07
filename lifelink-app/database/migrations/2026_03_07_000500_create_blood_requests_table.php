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
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients', 'patient_id');
            $table->foreignId('admission_id')->nullable()->constrained('admissions');
            $table->foreignId('department_id')->constrained('departments');
            $table->foreignId('requested_by_user_id')->constrained('users');
            $table->string('blood_group_needed', 5);
            $table->string('component_type', 30)->default('WholeBlood');
            $table->unsignedInteger('units_required');
            $table->string('urgency', 20)->default('Urgent');
            $table->string('status', 20)->default('Pending');
            $table->timestamp('request_date')->useCurrent();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'status']);
            $table->index(['department_id', 'status']);
            $table->index('request_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
