<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_locked')->default(false);
            $table->integer('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->boolean('password_force_change')->default(false);
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->boolean('is_public_submission')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn('is_public_submission');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_locked', 'failed_login_attempts', 'locked_until', 'password_force_change']);
        });
    }
};
