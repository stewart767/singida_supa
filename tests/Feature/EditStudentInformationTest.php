<?php

namespace Tests\Feature;

use App\Models\AcademicProfile;
use App\Models\AcademicYear;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationDocument;
use App\Models\Intake;
use App\Models\Permission;
use App\Models\Programme;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EditStudentInformationTest extends TestCase
{
    use RefreshDatabase;

    protected User $adminUser;
    protected Application $application;
    protected Programme $programme;
    protected AcademicYear $academicYear;
    protected Intake $intake;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        // Create Admin User with permissions
        $this->adminUser = User::factory()->create([
            'email' => 'admin@singida.ac.tz',
            'role' => 'admin',
        ]);

        $adminRole = Role::firstOrCreate(['name' => 'admin', 'display_name' => 'Administrator']);
        $perms = ['view_dashboard', 'manage_applications', 'verify_documents', 'make_admission_decisions', 'manage_settings', 'download_reports'];
        foreach ($perms as $p) {
            $permission = Permission::firstOrCreate(['name' => $p, 'display_name' => ucwords(str_replace('_', ' ', $p))]);
            $adminRole->permissions()->syncWithoutDetaching([$permission->id]);
        }
        $this->adminUser->roles()->sync([$adminRole->id]);

        // Create prerequisites
        $this->programme = Programme::create([
            'code' => 'BSc-CS',
            'name' => 'Bachelor of Science in Computer Science',
            'degree_level' => 'Bachelor',
            'duration_years' => 3,
            'tuition_fee_tzs' => 1500000,
            'is_active' => true,
        ]);

        $this->academicYear = AcademicYear::create([
            'code' => '2026/2027',
            'name' => '2026/2027',
            'is_active' => true,
            'is_current' => true,
            'start_date' => '2026-10-01',
            'end_date' => '2027-09-30',
            'application_deadline' => '2026-11-30',
        ]);

        $this->intake = Intake::create([
            'name' => 'October Intake',
            'code' => 'OCT-2026',
            'is_active' => true,
        ]);

        // Create Applicant and Application
        $studentUser = User::factory()->create([
            'name' => 'Juma Stewart Bakari',
            'email' => 'juma.stewart@gmail.com',
            'phone' => '+255712345678',
            'role' => 'applicant',
        ]);

        $applicant = Applicant::create([
            'user_id' => $studentUser->id,
            'gender' => 'male',
            'date_of_birth' => '2001-05-15',
            'nida_number' => '19990101123450000123',
            'region' => 'Singida',
            'district' => 'Singida Mjini',
            'ward' => 'Mandewa',
            'nationality' => 'Tanzanian',
            'next_of_kin_name' => 'Bakari Juma',
            'next_of_kin_phone' => '+255755112233',
            'next_of_kin_relation' => 'Father',
        ]);

        $this->application = Application::create([
            'application_number' => 'SUPA-2026-000100',
            'applicant_id' => $applicant->id,
            'programme_id' => $this->programme->id,
            'academic_year_id' => $this->academicYear->id,
            'intake_id' => $this->intake->id,
            'admission_type' => 'Form Six',
            'admission_category' => 'Direct Entry',
            'status' => 'Under Review',
            'completion_percentage' => 100,
            'current_step' => 8,
            'submitted_at' => now(),
        ]);

        AcademicProfile::create([
            'application_id' => $this->application->id,
            'admission_type' => 'Form Six',
            'csee_number' => 'S0101/0001/2018',
            'csee_year' => 2018,
            'csee_school' => 'Singida Secondary School',
            'acsee_number' => 'S0101/0501/2020',
            'acsee_year' => 2020,
            'acsee_school' => 'Singida High School',
            'acsee_combination' => 'PCB',
            'acsee_subject1' => 'Physics',
            'acsee_grade1' => 'C',
            'acsee_subject2' => 'Chemistry',
            'acsee_grade2' => 'C',
            'acsee_subject3' => 'Biology',
            'acsee_grade3' => 'D',
            'acsee_gs_grade' => 'C',
            'acsee_points' => 8,
        ]);
    }

    public function test_admin_can_view_edit_student_application_page(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.applications.edit', $this->application->id));

        $response->assertStatus(200);
        $response->assertSee('Edit Student Profile', false);
        $response->assertSee('Juma Stewart Bakari');
        $response->assertSee('SUPA-2026-000100');
        $response->assertSee('Bachelor of Science in Computer Science');
    }

    public function test_admin_can_update_student_personal_information(): void
    {
        $payload = [
            'name' => 'Juma Stewart Bakari Updated',
            'email' => 'juma.updated@gmail.com',
            'phone' => '+255799887766',
            'gender' => 'male',
            'date_of_birth' => '2001-05-15',
            'nida_number' => '20010515123450000999',
            'voter_id_number' => 'VOTER-12345',
            'work_id_number' => 'EMP-999',
            'whatsapp_number' => '+255799887766',
            'region' => 'Dodoma',
            'district' => 'Dodoma Mjini',
            'ward' => 'Kizota',
            'nationality' => 'Tanzanian',
            'next_of_kin_name' => 'Mariam Bakari',
            'next_of_kin_phone' => '+255711223344',
            'next_of_kin_relation' => 'Mother',
            'programme_id' => $this->programme->id,
            'academic_year_id' => $this->academicYear->id,
            'intake_id' => $this->intake->id,
            'admission_type' => 'Form Six',
            'status' => 'Under Review',
            'csee_number' => 'S0101/0001/2018',
            'csee_year' => 2018,
            'csee_school' => 'Singida Secondary School',
            'acsee_number' => 'S0101/0501/2020',
            'acsee_year' => 2020,
            'acsee_school' => 'Singida High School',
            'acsee_combination' => 'PCB',
            'acsee_subject1' => 'Physics',
            'acsee_grade1' => 'C',
            'acsee_subject2' => 'Chemistry',
            'acsee_grade2' => 'C',
            'acsee_subject3' => 'Biology',
            'acsee_grade3' => 'C',
            'acsee_gs_grade' => 'C',
            'acsee_points' => 9,
        ];

        $response = $this->actingAs($this->adminUser)->put(route('admin.applications.update', $this->application->id), $payload);

        $response->assertRedirect(route('admin.applications.show', $this->application->id));

        $this->assertDatabaseHas('users', [
            'name' => 'Juma Stewart Bakari Updated',
            'email' => 'juma.updated@gmail.com',
            'phone' => '+255799887766',
        ]);

        $this->assertDatabaseHas('applicants', [
            'region' => 'Dodoma',
            'district' => 'Dodoma Mjini',
            'ward' => 'Kizota',
            'voter_id_number' => 'VOTER-12345',
            'work_id_number' => 'EMP-999',
            'next_of_kin_name' => 'Mariam Bakari',
        ]);
    }

    public function test_admin_can_update_academic_qualifications_with_auto_category_diploma(): void
    {
        // Update to Diploma with GPA 3.5 -> Direct Entry
        $payload = [
            'name' => 'Juma Stewart Bakari',
            'email' => 'juma.stewart@gmail.com',
            'phone' => '+255712345678',
            'gender' => 'male',
            'date_of_birth' => '2001-05-15',
            'programme_id' => $this->programme->id,
            'academic_year_id' => $this->academicYear->id,
            'intake_id' => $this->intake->id,
            'admission_type' => 'Diploma',
            'status' => 'Under Review',
            'college_name' => 'Singida Teachers College',
            'diploma_programme_name' => 'Diploma in Computer Science',
            'diploma_registration_number' => 'REG/DCS/2023/001',
            'diploma_graduation_year' => 2023,
            'gpa' => 3.55,
        ];

        $response = $this->actingAs($this->adminUser)->put(route('admin.applications.update', $this->application->id), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'admission_type' => 'Diploma',
            'admission_category' => 'Direct Entry',
        ]);

        $this->assertDatabaseHas('academic_profiles', [
            'application_id' => $this->application->id,
            'college_name' => 'Singida Teachers College',
            'diploma_programme_name' => 'Diploma in Computer Science',
            'gpa' => 3.55,
        ]);

        // Update to Diploma with GPA 2.4 -> Foundation
        $payload['gpa'] = 2.40;
        $this->actingAs($this->adminUser)->put(route('admin.applications.update', $this->application->id), $payload);

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'admission_category' => 'Foundation',
        ]);
    }

    public function test_admin_can_update_academic_qualifications_with_auto_category_form_six(): void
    {
        // Form Six with 2 Principal Passes (C, D, F) -> Direct Entry
        $payload = [
            'name' => 'Juma Stewart Bakari',
            'email' => 'juma.stewart@gmail.com',
            'phone' => '+255712345678',
            'gender' => 'male',
            'date_of_birth' => '2001-05-15',
            'programme_id' => $this->programme->id,
            'academic_year_id' => $this->academicYear->id,
            'intake_id' => $this->intake->id,
            'admission_type' => 'Form Six',
            'status' => 'Under Review',
            'csee_number' => 'S0101/0001/2018',
            'csee_year' => 2018,
            'csee_school' => 'Singida Secondary School',
            'acsee_number' => 'S0101/0501/2020',
            'acsee_year' => 2020,
            'acsee_school' => 'Singida High School',
            'acsee_combination' => 'PCB',
            'acsee_subject1' => 'Physics',
            'acsee_grade1' => 'C',
            'acsee_subject2' => 'Chemistry',
            'acsee_grade2' => 'D',
            'acsee_subject3' => 'Biology',
            'acsee_grade3' => 'F',
            'acsee_gs_grade' => 'S',
            'acsee_points' => 5,
        ];

        $response = $this->actingAs($this->adminUser)->put(route('admin.applications.update', $this->application->id), $payload);
        $response->assertRedirect();

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'admission_type' => 'Form Six',
            'admission_category' => 'Direct Entry',
        ]);

        // Form Six with 0 or 1 Principal Pass (E.g. Grade F, F, S) -> Foundation
        $payload['acsee_grade1'] = 'F';
        $payload['acsee_grade2'] = 'S';
        $payload['acsee_grade3'] = 'F';

        $this->actingAs($this->adminUser)->put(route('admin.applications.update', $this->application->id), $payload);

        $this->assertDatabaseHas('applications', [
            'id' => $this->application->id,
            'admission_category' => 'Foundation',
        ]);
    }

    public function test_admin_can_upload_student_certificate_document(): void
    {
        $file = UploadedFile::fake()->create('form_four_cert.pdf', 500, 'application/pdf');

        $response = $this->actingAs($this->adminUser)->post(
            route('admin.applications.documents.store', $this->application->id),
            [
                'document_type' => 'form_four_certificate',
                'document' => $file,
                'verification_status' => 'verified',
            ]
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('application_documents', [
            'application_id' => $this->application->id,
            'document_type' => 'form_four_certificate',
            'verification_status' => 'verified',
            'original_filename' => 'form_four_cert.pdf',
        ]);
    }

    public function test_admin_can_replace_existing_student_document(): void
    {
        $initialFile = UploadedFile::fake()->create('old_cert.pdf', 300, 'application/pdf');
        $path = $initialFile->store('documents/test', 'public');

        $doc = ApplicationDocument::create([
            'application_id' => $this->application->id,
            'document_type' => 'birth_certificate',
            'original_filename' => 'old_cert.pdf',
            'file_path' => $path,
            'file_size_bytes' => 300 * 1024,
            'mime_type' => 'application/pdf',
            'verification_status' => 'pending',
        ]);

        $replacementFile = UploadedFile::fake()->create('new_birth_cert.pdf', 400, 'application/pdf');

        $response = $this->actingAs($this->adminUser)->post(
            route('admin.documents.replace', $doc->id),
            [
                'document' => $replacementFile,
                'verification_status' => 'verified',
            ]
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('application_documents', [
            'id' => $doc->id,
            'original_filename' => 'new_birth_cert.pdf',
            'verification_status' => 'verified',
        ]);
    }

    public function test_admin_can_delete_student_document(): void
    {
        $initialFile = UploadedFile::fake()->create('cert_to_delete.pdf', 200, 'application/pdf');
        $path = $initialFile->store('documents/test', 'public');

        $doc = ApplicationDocument::create([
            'application_id' => $this->application->id,
            'document_type' => 'leaving_certificate',
            'original_filename' => 'cert_to_delete.pdf',
            'file_path' => $path,
            'file_size_bytes' => 200 * 1024,
            'mime_type' => 'application/pdf',
            'verification_status' => 'verified',
        ]);

        $response = $this->actingAs($this->adminUser)->delete(route('admin.documents.destroy', $doc->id));

        $response->assertRedirect();

        $this->assertDatabaseMissing('application_documents', [
            'id' => $doc->id,
        ]);
    }

    public function test_non_admin_cannot_access_student_edit_routes(): void
    {
        $regularUser = User::factory()->create(['role' => 'applicant']);

        $response = $this->actingAs($regularUser)->get(route('admin.applications.edit', $this->application->id));
        $response->assertStatus(403);

        $response = $this->actingAs($regularUser)->put(route('admin.applications.update', $this->application->id), []);
        $response->assertStatus(403);
    }
}
