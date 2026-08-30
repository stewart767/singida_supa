<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Intake;
use App\Models\Programme;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Mail\DocumentRejectedMail;

class AdmissionWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $activeTerms = \App\Models\TermsCondition::where('status', 'Published')->latest('effective_date')->first();

        \App\Models\Applicant::query()->update([
            'initial_consent_given' => true,
            'consent_status' => 'accepted',
            'consented_at' => now(),
            'privacy_policy_version' => $activePolicy ? $activePolicy->version : '2.1',
            'terms_version' => $activeTerms ? $activeTerms->version : '2.1',
        ]);
    }

    public function test_public_can_fetch_programmes()
    {
        $response = $this->getJson('/api/v1/public/programmes');
        $response->assertStatus(200)
                 ->assertJsonStructure(['programmes']);
    }

    public function test_can_save_personal_info_with_any_valid_id()
    {
        $applicantUser = User::where('role', 'applicant')->first();

        $this->actingAs($applicantUser);

        // 1. With Voter ID alone (should succeed)
        $response = $this->postJson('/api/v1/applicant/personal-info', [
            'gender' => 'female',
            'date_of_birth' => '1995-08-15',
            'nida_number' => '',
            'voter_id_number' => 'T-1234-5678-901',
            'work_id_number' => '',
            'region' => 'Singida',
            'district' => 'Singida Manispaa',
            'ward' => 'Majengo',
            'next_of_kin_name' => 'Jane Kin',
            'next_of_kin_phone' => '+255788112233',
            'next_of_kin_relation' => 'Mother',
        ]);

        $response->assertStatus(200)
                 ->assertJsonPath('applicant.voter_id_number', 'T-1234-5678-901')
                 ->assertJsonPath('applicant.ward', 'Majengo');

        $this->assertDatabaseHas('applicants', [
            'user_id' => $applicantUser->id,
            'voter_id_number' => 'T-1234-5678-901',
            'ward' => 'Majengo',
        ]);

        // 2. With all 3 IDs empty (should fail validation)
        $failResponse = $this->postJson('/api/v1/applicant/personal-info', [
            'gender' => 'female',
            'date_of_birth' => '1995-08-15',
            'nida_number' => '',
            'voter_id_number' => '',
            'work_id_number' => '',
            'region' => 'Singida',
            'district' => 'Singida Manispaa',
            'ward' => 'Majengo',
        ]);

        $failResponse->assertStatus(422)
                     ->assertJsonValidationErrors(['nida_number']);
    }

    public function test_can_save_guest_credentials_with_optional_whatsapp()
    {
        $guestUser = User::create([
            'name' => 'Guest User',
            'email' => 'temp-guest-test@supa-guest.com',
            'phone' => '+255700000099',
            'role' => 'applicant',
            'is_active' => true,
            'password' => bcrypt('password'),
        ]);

        $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $activeTerms = \App\Models\TermsCondition::where('status', 'Published')->latest('effective_date')->first();

        \App\Models\Applicant::create([
            'user_id' => $guestUser->id,
            'initial_consent_given' => true,
            'consent_status' => 'accepted',
            'consented_at' => now(),
            'privacy_policy_version' => $activePolicy ? $activePolicy->version : '2.1',
            'terms_version' => $activeTerms ? $activeTerms->version : '2.1',
        ]);

        $this->actingAs($guestUser);

        // Test with empty WhatsApp (should pass)
        $response = $this->withSession(['guest_user_id' => $guestUser->id])
            ->postJson('/applicant/guest-credentials', [
                'name' => 'Juma Juma',
                'email' => 'juma.test@example.com',
                'phone' => '0712345678',
                'whatsapp_number' => '',
                'consent_given' => true,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('users', ['email' => 'juma.test@example.com', 'phone' => '0712345678']);

        // Test with valid Tanzanian WhatsApp (should pass and save)
        $response = $this->withSession(['guest_user_id' => $guestUser->id])
            ->postJson('/applicant/guest-credentials', [
                'name' => 'Juma Juma',
                'email' => 'juma.test@example.com',
                'phone' => '0712345678',
                'whatsapp_number' => '0754123456',
                'consent_given' => true,
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('applicants', ['user_id' => $guestUser->id, 'whatsapp_number' => '0754123456']);
    }

    public function test_automatic_admission_category_calculation_for_diploma()
    {
        $applicantUser = User::where('role', 'applicant')->first();

        $this->actingAs($applicantUser);

        // Save Personal Info
        $this->postJson('/api/v1/applicant/personal-info', [
            'gender' => 'male',
            'date_of_birth' => '2000-01-01',
            'nida_number' => '20000101123450000112',
            'region' => 'Dar es Salaam',
            'district' => 'Kinondoni',
            'next_of_kin_name' => 'John Doe',
            'next_of_kin_phone' => '+255700000000',
            'next_of_kin_relation' => 'Father',
        ])->assertStatus(200);

        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        // High GPA -> Direct Entry
        $res = $this->postJson('/api/v1/applicant/academic-profile', [
            'admission_type' => 'Diploma',
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'college_name' => 'DIT',
            'diploma_programme_name' => 'Diploma in Education',
            'diploma_registration_number' => '12345',
            'diploma_graduation_year' => 2023,
            'gpa' => 3.5,
        ]);

        $res->assertStatus(200)
            ->assertJson(['admission_category' => 'Direct Entry'])
            ->assertJsonPath('application.payment.id', fn ($id) => !empty($id))
            ->assertJsonPath('application.payment.control_number', fn ($num) => !empty($num));
    }

    public function test_automatic_admission_category_calculation_for_form_six()
    {
        $applicantUser = User::where('role', 'applicant')->first();

        $this->actingAs($applicantUser);

        // Save Personal Info
        $this->postJson('/api/v1/applicant/personal-info', [
            'gender' => 'male',
            'date_of_birth' => '2000-01-01',
            'nida_number' => '20000101123450000112',
            'region' => 'Dar es Salaam',
            'district' => 'Kinondoni',
            'next_of_kin_name' => 'John Doe',
            'next_of_kin_phone' => '+255700000000',
            'next_of_kin_relation' => 'Father',
        ])->assertStatus(200);

        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        // Form Six -> Direct Entry
        $res = $this->postJson('/api/v1/applicant/academic-profile', [
            'admission_type' => 'Form Six',
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'csee_number' => 'S0101/0001/2020',
            'csee_year' => 2020,
            'csee_school' => 'Macechu Secondary',
            'acsee_number' => 'S0101/0001/2023',
            'acsee_year' => 2023,
            'acsee_school' => 'Tabora Boys',
            'acsee_combination' => 'PCB',
            'acsee_grade1' => 'B',
            'acsee_grade2' => 'C',
            'acsee_grade3' => 'D',
        ]);

        $res->assertStatus(200)
            ->assertJson(['admission_category' => 'Direct Entry'])
            ->assertJsonPath('application.payment.id', fn ($id) => !empty($id))
            ->assertJsonPath('application.payment.control_number', fn ($num) => !empty($num));

        $this->assertDatabaseHas('academic_profiles', [
            'acsee_combination' => 'PCB',
            'acsee_subject1' => 'Physics',
            'acsee_subject2' => 'Chemistry',
            'acsee_subject3' => 'Biology',
            'acsee_grade1' => 'B',
            'acsee_grade2' => 'C',
            'acsee_grade3' => 'D',
        ]);

        // Form Six -> Foundation Entry
        $foundationProgramme = Programme::where('code', 'Foundation')->first();
        if ($foundationProgramme) {
            $res2 = $this->postJson('/api/v1/applicant/academic-profile', [
                'admission_type' => 'Form Six',
                'programme_id' => $foundationProgramme->id,
                'academic_year_id' => $academicYear->id,
                'intake_id' => $intake->id,
                'csee_number' => 'S0101/0001/2020',
                'csee_year' => 2020,
                'csee_school' => 'Macechu Secondary',
                'acsee_number' => 'S0101/0001/2023',
                'acsee_year' => 2023,
                'acsee_school' => 'Tabora Boys',
                'acsee_combination' => 'PCB',
                'acsee_subject1' => 'Physics',
                'acsee_subject2' => 'Chemistry',
                'acsee_subject3' => 'Biology',
                'acsee_grade1' => 'S',
                'acsee_grade2' => 'S',
                'acsee_grade3' => 'F',
                'acsee_gs_grade' => 'C',
            ]);

            $res2->assertStatus(200)
                ->assertJson(['admission_category' => 'Foundation']);

            $this->assertDatabaseHas('academic_profiles', [
                'acsee_combination' => 'PCB',
                'acsee_subject1' => 'Physics',
                'acsee_subject2' => 'Chemistry',
                'acsee_subject3' => 'Biology',
                'acsee_grade1' => 'S',
                'acsee_grade2' => 'S',
                'acsee_grade3' => 'F',
                'acsee_gs_grade' => 'C',
            ]);
        }
    }

    public function test_document_upload_and_admin_retrieval()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $applicantUser = User::where('role', 'applicant')->first();
        $this->actingAs($applicantUser);

        // Reset application to Draft and delete its documents for a clean wizard test state
        if ($applicantUser->applicant) {
            $app = \App\Models\Application::where('applicant_id', $applicantUser->applicant->id)->latest('id')->first();
            if ($app) {
                $app->update(['status' => 'Draft']);
                $app->documents()->delete();
            }
        }

        // Save Personal Info
        $this->postJson('/api/v1/applicant/personal-info', [
            'gender' => 'male',
            'date_of_birth' => '2000-01-01',
            'nida_number' => '20000101123450000112',
            'region' => 'Dar es Salaam',
            'district' => 'Kinondoni',
            'next_of_kin_name' => 'John Doe',
            'next_of_kin_phone' => '+255700000000',
            'next_of_kin_relation' => 'Father',
        ])->assertStatus(200);

        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        $this->postJson('/api/v1/applicant/academic-profile', [
            'admission_type' => 'Diploma',
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'college_name' => 'DIT',
            'diploma_programme_name' => 'Diploma in Education',
            'diploma_registration_number' => '12345',
            'diploma_graduation_year' => 2023,
            'gpa' => 3.5,
        ])->assertStatus(200);

        // Upload Document
        $file = \Illuminate\Http\UploadedFile::fake()->create('csee_cert.pdf', 500, 'application/pdf');

        $res = $this->postJson('/applicant/upload-document', [
            'document_type' => 'csee_certificate',
            'document' => $file,
        ]);

        $res->assertStatus(200)
            ->assertJsonStructure([
                'message',
                'document' => [
                    'id',
                    'application_id',
                    'document_type',
                    'original_filename',
                    'file_path',
                    'file_size_bytes',
                    'mime_type',
                ]
            ]);

        // Assert file was stored on disk
        $doc = $res->json('document');
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($doc['file_path']);

        // Admin retrieves application and lists documents
        $adminUser = User::where('role', 'super_admin')->first();
        $this->actingAs($adminUser);

        $appId = $doc['application_id'];
        $adminRes = $this->getJson("/api/v1/admin/applications/{$appId}");
        $adminRes->assertStatus(200)
                 ->assertJsonCount(1, 'application.documents')
                 ->assertJsonPath('application.documents.0.document_type', 'csee_certificate')
                 ->assertJsonPath('application.documents.0.original_filename', 'csee_cert.pdf');
    }

    public function test_admin_can_reject_document_and_notifies_applicant()
    {
        \Illuminate\Support\Facades\Mail::fake();
        \Illuminate\Support\Facades\Storage::fake('public');

        $applicantUser = User::where('role', 'applicant')->first();
        $this->actingAs($applicantUser);

        // Reset application to Draft and delete its documents for a clean wizard test state
        if ($applicantUser->applicant) {
            $app = \App\Models\Application::where('applicant_id', $applicantUser->applicant->id)->latest('id')->first();
            if ($app) {
                $app->update(['status' => 'Draft']);
                $app->documents()->delete();
            }
        }

        // Save Personal Info
        $this->postJson('/api/v1/applicant/personal-info', [
            'gender' => 'male',
            'date_of_birth' => '2000-01-01',
            'nida_number' => '20000101123450000112',
            'region' => 'Dar es Salaam',
            'district' => 'Kinondoni',
            'next_of_kin_name' => 'John Doe',
            'next_of_kin_phone' => '+255700000000',
            'next_of_kin_relation' => 'Father',
        ])->assertStatus(200);

        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        $this->postJson('/api/v1/applicant/academic-profile', [
            'admission_type' => 'Diploma',
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'college_name' => 'DIT',
            'diploma_programme_name' => 'Diploma in Education',
            'diploma_registration_number' => '12345',
            'diploma_graduation_year' => 2023,
            'gpa' => 3.5,
        ])->assertStatus(200);

        // Upload Document
        $file = \Illuminate\Http\UploadedFile::fake()->create('csee_cert.pdf', 500, 'application/pdf');
        $uploadRes = $this->postJson('/applicant/upload-document', [
            'document_type' => 'csee_certificate',
            'document' => $file,
        ]);
        $uploadRes->assertStatus(200);
        $docId = $uploadRes->json('document.id');

        // Admin logs in and rejects document
        $adminUser = User::where('role', 'super_admin')->first();
        $this->actingAs($adminUser);

        $verifyRes = $this->postJson("/api/v1/admin/documents/{$docId}/verify", [
            'status' => 'rejected',
            'rejection_comment' => 'Image name does not match profile name',
        ]);
        $verifyRes->assertStatus(200);

        // Assert mail was sent to applicant
        \Illuminate\Support\Facades\Mail::assertSent(DocumentRejectedMail::class, function ($mail) use ($applicantUser) {
            return $mail->hasTo($applicantUser->email) &&
                   $mail->docName === 'csee certificate' &&
                   $mail->comment === 'Image name does not match profile name';
        });

        // Student views dashboard
        $this->actingAs($applicantUser);
        $dashRes = $this->get('/applicant/dashboard');
        $dashRes->assertStatus(200)
                ->assertSee('Action Required: Rejected Documents')
                ->assertSee('Image name does not match profile name');

        // Student re-uploads document
        $newFile = \Illuminate\Http\UploadedFile::fake()->create('csee_cert_fixed.pdf', 600, 'application/pdf');
        $reuploadRes = $this->postJson('/applicant/upload-document', [
            'document_type' => 'csee_certificate',
            'document' => $newFile,
        ]);
        $reuploadRes->assertStatus(200)
                   ->assertJsonPath('document.verification_status', 'pending')
                   ->assertJsonPath('document.rejection_comment', null);
    }

    public function test_gpa_eligibility_validation()
    {
        $applicantUser = User::where('role', 'applicant')->first();
        $this->actingAs($applicantUser);

        // Save Personal Info
        $this->postJson('/api/v1/applicant/personal-info', [
            'gender' => 'male',
            'date_of_birth' => '2000-01-01',
            'nida_number' => '20000101123450000112',
            'region' => 'Singida',
            'district' => 'Singida',
            'next_of_kin_name' => 'John Doe',
            'next_of_kin_phone' => '+255700000000',
            'next_of_kin_relation' => 'Father',
        ])->assertStatus(200);

        $baed = Programme::where('code', 'BAED')->first();
        $foundation = Programme::where('code', 'Foundation')->first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        // 1. GPA < 2.0 -> should fail validation completely
        $res = $this->postJson('/api/v1/applicant/academic-profile', [
            'admission_type' => 'Diploma',
            'programme_id' => $foundation->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'college_name' => 'DIT',
            'diploma_programme_name' => 'Diploma in Education',
            'diploma_registration_number' => '12345',
            'diploma_graduation_year' => 2023,
            'gpa' => 1.8,
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['gpa']);

        // 2. GPA 2.5 (Foundation range) but selecting BAED -> should fail validation
        $res = $this->postJson('/api/v1/applicant/academic-profile', [
            'admission_type' => 'Diploma',
            'programme_id' => $baed->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'college_name' => 'DIT',
            'diploma_programme_name' => 'Diploma in Education',
            'diploma_registration_number' => '12345',
            'diploma_graduation_year' => 2023,
            'gpa' => 2.5,
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['programme_id']);

        // 3. GPA 2.5 selecting Foundation Course -> should pass validation
        $res = $this->postJson('/api/v1/applicant/academic-profile', [
            'admission_type' => 'Diploma',
            'programme_id' => $foundation->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'college_name' => 'DIT',
            'diploma_programme_name' => 'Diploma in Education',
            'diploma_registration_number' => '12345',
            'diploma_graduation_year' => 2023,
            'gpa' => 2.5,
        ]);
        $res->assertStatus(200)
            ->assertJson(['admission_category' => 'Foundation']);

        // 4. GPA 3.5 selecting Foundation Course -> should fail validation (must choose degree)
        $res = $this->postJson('/api/v1/applicant/academic-profile', [
            'admission_type' => 'Diploma',
            'programme_id' => $foundation->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'college_name' => 'DIT',
            'diploma_programme_name' => 'Diploma in Education',
            'diploma_registration_number' => '12345',
            'diploma_graduation_year' => 2023,
            'gpa' => 3.5,
        ]);
        $res->assertStatus(422)
            ->assertJsonValidationErrors(['programme_id']);
    }

    public function test_can_save_academic_profile_with_only_combination_and_points()
    {
        $applicantUser = User::where('role', 'applicant')->first();
        $this->actingAs($applicantUser);

        $programme = Programme::where('code', 'BAED')->first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        $res = $this->postJson('/api/v1/applicant/academic-profile', [
            'admission_type' => 'Form Six',
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'csee_number' => 'S0101/0001/2020',
            'csee_year' => 2020,
            'csee_school' => 'Macechu Secondary',
            'acsee_number' => 'S0101/0001/2023',
            'acsee_year' => 2023,
            'acsee_school' => 'Tabora Boys',
            'acsee_combination' => 'ECA',
            'acsee_points' => 9,
            'acsee_gs_grade' => 'C',
        ]);

        $res->assertStatus(200)
            ->assertJson(['admission_category' => 'Direct Entry']);

        $this->assertDatabaseHas('academic_profiles', [
            'acsee_combination' => 'ECA',
            'acsee_points' => 9,
            'acsee_gs_grade' => 'C',
            'acsee_subject1' => 'Economics',
            'acsee_subject2' => 'Commerce',
            'acsee_subject3' => 'Accountancy',
        ]);
    }

    public function test_admin_can_manually_add_student_admission()
    {
        $adminUser = User::where('role', 'super_admin')->first();
        $this->actingAs($adminUser);

        $programme = Programme::where('code', 'BAED')->first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        $response = $this->postJson(route('admin.applications.store'), [
            'name' => 'Test Student Manual',
            'email' => 'manual.student.test@supa.ac.tz',
            'phone' => '+255755999999',
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'admission_type' => 'Diploma',
            'admission_category' => 'Direct Entry',
            'status' => 'Approved',
            'gender' => 'female',
            'date_of_birth' => '2002-04-10',
        ]);

        $response->assertStatus(200)
                 ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'email' => 'manual.student.test@supa.ac.tz',
            'role' => 'applicant',
        ]);

        $user = User::where('email', 'manual.student.test@supa.ac.tz')->first();
        $this->assertNotNull($user->applicant);

        $this->assertDatabaseHas('applications', [
            'applicant_id' => $user->applicant->id,
            'programme_id' => $programme->id,
            'status' => 'Approved',
        ]);

        // Assert admission letter was automatically generated since status is Approved
        $application = \App\Models\Application::where('applicant_id', $user->applicant->id)->first();
        $this->assertNotNull($application->admissionLetter);
    }
}

