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
        Schema::create('nurses', function (Blueprint $table) {
            $table->foreignId('nurse_id')->primary()->constrained('users')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments');
            $table->string('ward_assignment_note', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('department_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nurses');
    }
};
