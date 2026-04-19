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
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->foreignId('blood_bank_id')->nullable()->constrained('blood_banks')->nullOnDelete();
            $table->index(['blood_bank_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blood_requests', function (Blueprint $table) {
            $table->dropForeign(['blood_bank_id']);
            $table->dropIndex(['blood_bank_id', 'status']);
            $table->dropColumn('blood_bank_id');
        });
    }
};
