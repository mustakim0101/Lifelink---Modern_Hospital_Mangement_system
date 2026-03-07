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
        Schema::create('blood_donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('donor_profiles', 'donor_id')->cascadeOnDelete();
            $table->foreignId('blood_bank_id')->constrained('blood_banks');
            $table->timestamp('donation_datetime');
            $table->string('blood_group', 5);
            $table->string('component_type', 30)->default('WholeBlood');
            $table->unsignedInteger('units_donated');
            $table->foreignId('recorded_by_user_id')->nullable()->constrained('users');
            $table->foreignId('linked_request_id')->nullable()->constrained('blood_requests')->nullOnDelete();
            $table->foreignId('donor_health_check_id')->nullable()->constrained('donor_health_checks');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['donor_id', 'donation_datetime']);
            $table->index(['blood_bank_id', 'blood_group', 'component_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_donations');
    }
};
