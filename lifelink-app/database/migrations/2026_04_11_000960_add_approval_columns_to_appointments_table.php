<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->date('appointment_date')->nullable()->after('doctor_user_id');
            $table->foreignId('approved_by_user_id')->nullable()->after('status')->constrained('users');
            $table->dateTime('approved_at')->nullable()->after('approved_by_user_id');
            $table->string('rejection_reason', 255)->nullable()->after('approved_at');

            $table->index(['doctor_user_id', 'appointment_date', 'status'], 'idx_appointments_doc_date_status');
            $table->index('appointment_date');
        });

        DB::statement('UPDATE appointments SET appointment_date = CONVERT(date, appointment_datetime) WHERE appointment_date IS NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->dropForeign(['approved_by_user_id']);
            $table->dropIndex('idx_appointments_doc_date_status');
            $table->dropIndex(['appointment_date']);
            $table->dropColumn([
                'appointment_date',
                'approved_by_user_id',
                'approved_at',
                'rejection_reason',
            ]);
        });
    }
};

