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
        Schema::create('donor_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donor_id')->constrained('donor_profiles', 'donor_id')->cascadeOnDelete();
            $table->date('week_start_date');
            $table->boolean('is_available')->default(true);
            $table->unsignedInteger('max_bags_possible')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['donor_id', 'week_start_date'], 'donor_availability_unique_donor_week');
            $table->index(['week_start_date', 'is_available']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_availabilities');
    }
};
