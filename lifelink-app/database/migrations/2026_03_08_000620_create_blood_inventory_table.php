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
        Schema::create('blood_inventory', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_bank_id')->constrained('blood_banks')->cascadeOnDelete();
            $table->string('blood_group', 5);
            $table->string('component_type', 30)->default('WholeBlood');
            $table->unsignedInteger('units_available')->default(0);
            $table->timestamp('last_updated_at')->nullable();
            $table->timestamps();

            $table->unique(['blood_bank_id', 'blood_group', 'component_type'], 'blood_inventory_unique_bank_group_component');
            $table->index(['blood_group', 'component_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blood_inventory');
    }
};
