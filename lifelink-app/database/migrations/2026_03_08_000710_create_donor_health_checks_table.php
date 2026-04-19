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
        Schema::create('donor_health_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('donor_profiles', 'donor_id')->cascadeOnDelete();
            $table->timestamp('check_datetime');
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('temperature_c', 4, 2);
            $table->decimal('hemoglobin', 4, 2)->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('checked_by_user_id')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['donor_id', 'check_datetime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_health_checks');
    }
};
