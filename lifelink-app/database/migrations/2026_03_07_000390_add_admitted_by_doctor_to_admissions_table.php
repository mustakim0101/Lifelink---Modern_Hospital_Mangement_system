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
        Schema::table('admissions', function (Blueprint $table) {
            $table->foreignId('admitted_by_doctor_id')->nullable()->after('department_id')->constrained('users');
            $table->index('admitted_by_doctor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('admissions', function (Blueprint $table) {
            $table->dropForeign(['admitted_by_doctor_id']);
            $table->dropIndex(['admitted_by_doctor_id']);
            $table->dropColumn('admitted_by_doctor_id');
        });
    }
};

