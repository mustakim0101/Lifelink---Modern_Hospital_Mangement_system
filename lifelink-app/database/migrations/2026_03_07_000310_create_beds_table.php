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
        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_unit_id')->constrained('care_units')->cascadeOnDelete();
            $table->string('bed_code', 50);
            $table->string('status', 20)->default('Available');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['care_unit_id', 'bed_code']);
            $table->index(['status', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beds');
    }
};

