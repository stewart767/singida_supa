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
        // 1. users table
        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable()->unique();
                $table->string('role')->default('applicant')->comment('super_admin, registrar, admission_officer, finance_officer, applicant');
                $table->boolean('ajira_linked')->default(false);
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->string('otp_code', 10)->nullable();
                $table->timestamp('otp_expires_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->string('avatar')->nullable();
                $table->boolean('is_locked')->default(false);
                $table->integer('failed_login_attempts')->default(0);
                $table->timestamp('locked_until')->nullable();
                $table->boolean('password_force_change')->default(false);
                $table->rememberToken();
                $table->timestamps();
                $table->softDeletes();
            });
        }

        // 2. sessions table
        if (!Schema::hasTable('sessions')) {
            Schema::create('sessions', function (Blueprint $table) {
                $table->string('id')->primary();
                $table->foreignId('user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->longText('payload');
                $table->integer('last_activity')->index();
            });
        }

        // 3. roles table
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }

        // 4. role_user table
        if (!Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $table) {
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
                $table->primary(['user_id', 'role_id']);
            });
        }

        // 5. settings table
        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->string('group')->default('general');
                $table->string('type')->default('string')->comment('string, number, boolean, json');
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // 6. vacancies table
        if (!Schema::hasTable('vacancies')) {
            Schema::create('vacancies', function (Blueprint $table) {
                $table->id();
                $table->string('vacancy_number')->unique();
                $table->string('job_title');
                $table->string('department_name')->nullable();
                $table->foreignId('designation_id')->constrained('designations')->onDelete('cascade');
                $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
                $table->foreignId('job_category_id')->constrained('job_categories')->onDelete('cascade');
                $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('cascade');
                $table->integer('number_of_positions')->default(1);
                $table->string('employment_type');
                $table->string('contract_type');
                $table->string('location');
                $table->string('recommended_region')->nullable();
                $table->string('salary_range')->nullable();
                $table->date('application_deadline');
                $table->date('closing_date')->nullable();
                $table->text('responsibilities');
                $table->text('qualifications');
                $table->text('required_experience');
                $table->text('required_skills');
                $table->text('benefits')->nullable();
                $table->string('featured_image')->nullable();
                $table->string('status')->default('Draft');
                $table->text('requirements')->nullable();
                $table->string('application_type')->default('internal');
                $table->string('external_url')->nullable();
                $table->string('external_provider')->nullable();
                $table->timestamps();
            });
        }

        // 7. written_tests table
        if (!Schema::hasTable('written_tests')) {
            Schema::create('written_tests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('job_application_id')->constrained('job_applications')->onDelete('cascade');
                $table->string('test_name');
                $table->date('assigned_date');
                $table->string('questions_file_path')->nullable();
                $table->string('script_file_path')->nullable();
                $table->float('marks')->nullable();
                $table->string('status')->default('Assigned');
                $table->text('comments')->nullable();
                $table->timestamps();
            });
        }

        // 8. talent_pools table
        if (!Schema::hasTable('talent_pools')) {
            Schema::create('talent_pools', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->string('category');
                $table->text('comments')->nullable();
                $table->timestamps();
            });
        }

        // 9. terms_conditions table
        if (!Schema::hasTable('terms_conditions')) {
            Schema::create('terms_conditions', function (Blueprint $table) {
                $table->id();
                $table->string('version')->unique();
                $table->string('title');
                $table->text('content')->nullable();
                $table->string('file_path')->nullable();
                $table->date('effective_date')->nullable();
                $table->enum('status', ['Draft', 'Published', 'Archived'])->default('Draft');
                $table->string('language', 10)->default('en');
                $table->foreignId('published_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('terms_conditions');
        Schema::dropIfExists('talent_pools');
        Schema::dropIfExists('written_tests');
        Schema::dropIfExists('vacancies');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('users');
    }
};
