<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->onDelete('cascade');
            $table->enum('gender', ['male', 'female', 'other'])->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('nida_number', 30)->nullable()->unique();
            $table->string('whatsapp_number', 30)->nullable();
            $table->string('region', 100)->nullable();
            $table->string('district', 100)->nullable();
            $table->string('ward', 100)->nullable();
            $table->string('nationality')->default('Tanzanian');
            $table->string('next_of_kin_name')->nullable();
            $table->string('next_of_kin_phone')->nullable();
            $table->string('next_of_kin_relation')->nullable();
            $table->string('passport_photo_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->string('application_number')->unique()->comment('e.g. SUPA/2026/00001');
            $table->foreignId('applicant_id')->constrained()->onDelete('cascade');
            $table->foreignId('programme_id')->constrained();
            $table->foreignId('academic_year_id')->constrained();
            $table->foreignId('intake_id')->constrained();
            $table->enum('admission_type', ['Diploma', 'Form Six'])->default('Form Six');
            $table->enum('admission_category', ['Direct Entry', 'Foundation'])->default('Direct Entry');
            $table->enum('status', [
                'Draft', 
                'Pending Payment', 
                'Under Review', 
                'Verified', 
                'Approved', 
                'Rejected', 
                'Waitlist'
            ])->default('Draft');
            $table->text('rejection_reason')->nullable();
            $table->string('digital_signature_path')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applications');
        Schema::dropIfExists('applicants');
    }
};
