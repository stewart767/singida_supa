<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Drop existing recruitment tables to allow fresh rebuild
        Schema::dropIfExists('talent_pools');
        Schema::dropIfExists('offer_letters');
        Schema::dropIfExists('written_tests');
        Schema::dropIfExists('interview_scorecards');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('job_application_stages');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('vacancies');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('job_categories');
        Schema::dropIfExists('campuses');

        // 2. Campuses Table
        Schema::create('campuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('location')->nullable();
            $table->string('status')->default('active'); // active, archived
            $table->timestamps();
        });

        // 3. Job Categories
        Schema::create('job_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, archived
            $table->integer('display_order')->default(0);
            $table->timestamps();
        });

        // 4. Designations
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('short_code')->unique();
            $table->foreignId('head_of_designation_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('description')->nullable();
            $table->string('status')->default('active'); // active, archived
            $table->timestamps();
        });

        // 5. Positions
        Schema::create('positions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('designation_id')->constrained('designations')->onDelete('cascade');
            $table->foreignId('job_category_id')->constrained('job_categories')->onDelete('cascade');
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('cascade');
            $table->string('employment_type'); // Full-time, Part-time, Contract, Internship
            $table->foreignId('reports_to_position_id')->nullable()->constrained('positions')->onDelete('set null');
            $table->string('salary_grade')->nullable();
            $table->string('status')->default('active'); // active, archived
            $table->timestamps();
        });

        // 6. Vacancies
        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->string('vacancy_number')->unique();
            $table->string('job_title');
            $table->foreignId('designation_id')->constrained('designations')->onDelete('cascade');
            $table->foreignId('position_id')->constrained('positions')->onDelete('cascade');
            $table->foreignId('job_category_id')->constrained('job_categories')->onDelete('cascade');
            $table->foreignId('campus_id')->nullable()->constrained('campuses')->onDelete('cascade');
            $table->integer('number_of_positions')->default(1);
            $table->string('employment_type');
            $table->string('contract_type'); // permanent, fixed-term, etc.
            $table->string('location');
            $table->string('salary_range')->nullable();
            $table->date('application_deadline');
            $table->date('closing_date')->nullable();
            $table->text('responsibilities');
            $table->text('qualifications');
            $table->text('required_experience');
            $table->text('required_skills');
            $table->text('benefits')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('status')->default('Draft'); // Draft, Published, Closed, Archived
            $table->text('requirements')->nullable(); // JSON configuration of documents, referees, stages
            $table->timestamps();
        });

        // 7. Job Applications (Supports 11-step wizard progress saving)
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vacancy_id')->constrained('vacancies')->onDelete('cascade');
            $table->string('status')->default('Applied'); // Applied, Screening, Under Review, Shortlisted, etc.
            $table->integer('current_step')->default(1); // Wizard save state step index 1 to 11
            
            // Step 1: Personal Info
            $table->string('full_name');
            $table->string('gender')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nida_number')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('nssf_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('whatsapp_number')->nullable();
            $table->string('email')->nullable();
            $table->string('region')->nullable();
            $table->string('district')->nullable();
            $table->text('physical_address')->nullable();

            // Step 2: Position (stored via Vacancy relationship, but position reference is available)
            
            // Step 3: Employment Experience
            $table->boolean('worked_at_sttc')->default(false);
            $table->text('sttc_experience')->nullable(); // JSON
            $table->text('experience_history')->nullable(); // JSON previous jobs

            // Step 4: Education
            $table->text('education_history')->nullable(); // JSON education records

            // Step 5: ICT Competency
            $table->text('ict_description')->nullable();
            $table->text('ict_skills')->nullable(); // JSON checklist items with levels

            // Step 6: Professional Qualifications
            $table->text('professional_qualifications')->nullable(); // JSON dynamic answers

            // Step 7: Referees
            $table->text('referees')->nullable(); // JSON minimum 3 referees

            // Step 8: Motivation Letter
            $table->text('motivation_letter')->nullable();

            // Step 9: Attachments
            $table->text('attachments')->nullable(); // JSON mapping file paths

            // Step 10: Declaration & Signature
            $table->boolean('certified_correct')->default(false);
            $table->text('digital_signature')->nullable(); // Base64 signature path or text
            $table->date('declaration_date')->nullable();

            // Step 11: Final submission
            $table->timestamp('submitted_at')->nullable();
            
            $table->foreignId('assigned_hr_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 8. Application Stage Logs
        Schema::create('job_application_stages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->onDelete('cascade');
            $table->string('stage');
            $table->foreignId('assigned_hr_officer_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('comments')->nullable();
            $table->text('attachments')->nullable(); // JSON
            $table->text('notification_history')->nullable(); // JSON
            $table->timestamps();
        });

        // 9. Interviews
        Schema::create('interviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->onDelete('cascade');
            $table->string('type'); // Physical, Online, Phone
            $table->date('date');
            $table->time('time');
            $table->string('venue')->nullable();
            $table->string('meeting_link')->nullable();
            $table->text('instructions')->nullable();
            $table->text('panel_members')->nullable(); // JSON user IDs
            $table->timestamps();
        });

        // 10. Scorecards
        Schema::create('interview_scorecards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interview_id')->constrained('interviews')->onDelete('cascade');
            $table->foreignId('interviewer_id')->constrained('users')->onDelete('cascade');
            $table->integer('communication')->default(0);
            $table->integer('technical_knowledge')->default(0);
            $table->integer('problem_solving')->default(0);
            $table->integer('leadership')->default(0);
            $table->integer('teamwork')->default(0);
            $table->integer('confidence')->default(0);
            $table->integer('professionalism')->default(0);
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        // 11. Written Tests
        Schema::create('written_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->onDelete('cascade');
            $table->string('test_name');
            $table->date('assigned_date');
            $table->string('questions_file_path')->nullable();
            $table->string('script_file_path')->nullable();
            $table->float('marks')->nullable();
            $table->string('status')->default('Assigned'); // Assigned, Completed
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        // 12. Offer Letters
        Schema::create('offer_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_application_id')->constrained('job_applications')->onDelete('cascade');
            $table->string('salary');
            $table->text('benefits');
            $table->date('reporting_date');
            $table->text('employment_terms');
            $table->string('pdf_path');
            $table->string('digital_signature_path')->nullable();
            $table->string('status')->default('Draft'); // Draft, Sent, Accepted, Declined
            $table->timestamps();
        });

        // 13. Talent Pools
        Schema::create('talent_pools', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('category');
            $table->text('comments')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('talent_pools');
        Schema::dropIfExists('offer_letters');
        Schema::dropIfExists('written_tests');
        Schema::dropIfExists('interview_scorecards');
        Schema::dropIfExists('interviews');
        Schema::dropIfExists('job_application_stages');
        Schema::dropIfExists('job_applications');
        Schema::dropIfExists('vacancies');
        Schema::dropIfExists('positions');
        Schema::dropIfExists('designations');
        Schema::dropIfExists('job_categories');
        Schema::dropIfExists('campuses');
    }
};
