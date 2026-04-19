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
        Schema::table('users', function (Blueprint $table) {
            $table->string('full_name')->nullable()->after('name');
            $table->string('phone', 30)->nullable()->after('email');
            $table->date('date_of_birth')->nullable()->after('phone');
            $table->string('gender', 20)->nullable()->after('date_of_birth');
            $table->string('account_status', 20)->default('Active')->after('password');
            $table->timestamp('frozen_at')->nullable()->after('remember_token');
            $table->foreignId('frozen_by_user_id')->nullable()->after('frozen_at')->constrained('users');

            $table->index('account_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['frozen_by_user_id']);
            $table->dropIndex(['account_status']);

            $table->dropColumn([
                'full_name',
                'phone',
                'date_of_birth',
                'gender',
                'account_status',
                'frozen_at',
                'frozen_by_user_id',
            ]);
        });
    }
};
