<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop foreign keys first to allow changing column type/nullability
        try {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropForeign(['programme_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropForeign(['academic_year_id']);
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('applications', function (Blueprint $table) {
                $table->dropForeign(['intake_id']);
            });
        } catch (\Exception $e) {}

        // Modify columns & Add new columns
        Schema::table('applications', function (Blueprint $table) {
            $table->unsignedBigInteger('programme_id')->nullable()->change();
            $table->unsignedBigInteger('academic_year_id')->nullable()->change();
            $table->unsignedBigInteger('intake_id')->nullable()->change();
            $table->string('status', 55)->default('Draft')->change();

            if (!Schema::hasColumn('applications', 'current_step')) {
                $table->integer('current_step')->default(1);
            }
            if (!Schema::hasColumn('applications', 'completion_percentage')) {
                $table->integer('completion_percentage')->default(0);
            }
            if (!Schema::hasColumn('applications', 'expires_at')) {
                $table->timestamp('expires_at')->nullable();
            }
            if (!Schema::hasColumn('applications', 'last_activity_at')) {
                $table->timestamp('last_activity_at')->nullable();
            }
        });

        // Recreate foreign keys
        Schema::table('applications', function (Blueprint $table) {
            // Check if foreign keys exist before recreating, or just try-catch them
            try {
                $table->foreign('programme_id')->references('id')->on('programmes');
            } catch (\Exception $e) {}
            
            try {
                $table->foreign('academic_year_id')->references('id')->on('academic_years');
            } catch (\Exception $e) {}

            try {
                $table->foreign('intake_id')->references('id')->on('intakes');
            } catch (\Exception $e) {}
        });

        // Update payments table payment_status
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_status', 55)->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['current_step', 'completion_percentage', 'expires_at', 'last_activity_at']);
        });
    }
};
