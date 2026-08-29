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
        // 1. Update privacy_policies table
        Schema::table('privacy_policies', function (Blueprint $table) {
            if (!Schema::hasColumn('privacy_policies', 'language')) {
                $table->string('language', 10)->default('en')->after('status');
            }
        });

        // 2. Update terms_conditions table
        Schema::table('terms_conditions', function (Blueprint $table) {
            if (!Schema::hasColumn('terms_conditions', 'language')) {
                $table->string('language', 10)->default('en')->after('status');
            }
            if (!Schema::hasColumn('terms_conditions', 'file_path')) {
                $table->string('file_path')->nullable()->after('content');
            }
            // Make content nullable
            $table->text('content')->nullable()->change();
        });

        // 3. Update application_consents table
        // Drop and re-add constraint to make application_id nullable
        Schema::table('application_consents', function (Blueprint $table) {
            $table->dropForeign(['application_id']);
        });

        Schema::table('application_consents', function (Blueprint $table) {
            $table->unsignedBigInteger('application_id')->nullable()->change();
            $table->foreign('application_id')
                ->references('id')
                ->on('applications')
                ->onDelete('cascade');

            if (!Schema::hasColumn('application_consents', 'applicant_id')) {
                $table->foreignId('applicant_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('applicants')
                    ->onDelete('cascade');
            }
        });

        // 4. Update applicants table
        Schema::table('applicants', function (Blueprint $table) {
            if (!Schema::hasColumn('applicants', 'consent_status')) {
                $table->string('consent_status', 30)->nullable()->after('initial_consent_at');
            }
            if (!Schema::hasColumn('applicants', 'consented_at')) {
                $table->timestamp('consented_at')->nullable()->after('consent_status');
            }
            if (!Schema::hasColumn('applicants', 'privacy_policy_version')) {
                $table->string('privacy_policy_version', 30)->nullable()->after('consented_at');
            }
            if (!Schema::hasColumn('applicants', 'terms_version')) {
                $table->string('terms_version', 30)->nullable()->after('privacy_policy_version');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse applicants updates
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropColumn(['consent_status', 'consented_at', 'privacy_policy_version', 'terms_version']);
        });

        // Reverse application_consents updates
        Schema::table('application_consents', function (Blueprint $table) {
            $table->dropForeign(['applicant_id']);
            $table->dropColumn(['applicant_id']);

            $table->dropForeign(['application_id']);
        });

        Schema::table('application_consents', function (Blueprint $table) {
            // Restore non-null and constraint
            $table->unsignedBigInteger('application_id')->nullable(false)->change();
            $table->foreign('application_id')
                ->references('id')
                ->on('applications')
                ->onDelete('cascade');
        });

        // Reverse terms_conditions updates
        Schema::table('terms_conditions', function (Blueprint $table) {
            $table->text('content')->nullable(false)->change();
            $table->dropColumn(['language', 'file_path']);
        });

        // Reverse privacy_policies updates
        Schema::table('privacy_policies', function (Blueprint $table) {
            $table->dropColumn(['language']);
        });
    }
};
