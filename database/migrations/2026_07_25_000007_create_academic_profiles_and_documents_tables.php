<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('academic_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->enum('admission_type', ['Diploma', 'Form Six']);
            
            // Diploma fields
            $table->string('college_name')->nullable();
            $table->string('diploma_programme_name')->nullable();
            $table->string('diploma_registration_number')->nullable();
            $table->integer('diploma_graduation_year')->nullable();
            $table->decimal('gpa', 4, 2)->nullable();
            
            // Form Six fields
            $table->string('csee_number')->nullable()->comment('Form IV Index');
            $table->integer('csee_year')->nullable();
            $table->string('csee_school')->nullable();
            $table->string('acsee_number')->nullable()->comment('Form VI Index');
            $table->integer('acsee_year')->nullable();
            $table->string('acsee_school')->nullable();
            $table->string('acsee_combination')->nullable();
            $table->integer('acsee_points')->nullable();

            $table->timestamps();
        });

        Schema::create('application_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('application_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', [
                'passport', 
                'csee_certificate', 
                'acsee_certificate', 
                'diploma_certificate', 
                'transcript', 
                'nida_id', 
                'payment_receipt'
            ]);
            $table->string('original_filename');
            $table->string('file_path');
            $table->integer('file_size_bytes');
            $table->string('mime_type');
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('rejection_comment')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users');
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_documents');
        Schema::dropIfExists('academic_profiles');
    }
};
