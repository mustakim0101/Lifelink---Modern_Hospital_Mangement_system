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
        Schema::create('donor_profiles', function (Blueprint $table) {
            $table->foreignId('donor_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->string('blood_group', 5);
            $table->timestamp('last_donation_date')->nullable();
            $table->boolean('is_eligible')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['blood_group', 'is_eligible']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donor_profiles');
    }
};
