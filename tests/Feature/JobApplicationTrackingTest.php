<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\JobCategory;
use App\Models\Designation;
use App\Models\Position;
use App\Models\Vacancy;
use App\Models\JobApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class JobApplicationTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected User $applicantUser;
    protected Vacancy $vacancy;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        \Illuminate\Support\Facades\Cache::flush();
        \App\Models\Setting::set('enable_recruitment_module', true, 'recruitment', 'boolean');
        \App\Models\Setting::set('enable_public_career_portal', true, 'recruitment', 'boolean');

        $applicantRole = Role::where('name', 'applicant')->first();
        $this->applicantUser = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0789654321',
            'ajira_linked' => true,
        ]);
        $this->applicantUser->roles()->attach($applicantRole);

        // Create Vacancy dependencies
        $category = JobCategory::create([
            'name' => 'IT & Engineering',
            'status' => 'active',
        ]);

        $designation = Designation::create([
            'name' => 'Information Technology',
            'short_code' => 'IT',
            'status' => 'active'
        ]);

        $position = Position::create([
            'name' => 'Laravel Developer',
            'designation_id' => $designation->id,
            'job_category_id' => $category->id,
            'employment_type' => 'Full-time',
            'status' => 'active'
        ]);

        $this->vacancy = Vacancy::create([
            'vacancy_number' => 'VAC-2026-IT001',
            'job_title' => 'Laravel Developer',
            'designation_id' => $designation->id,
            'position_id' => $position->id,
            'job_category_id' => $category->id,
            'number_of_positions' => 1,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida',
            'recommended_region' => 'Singida',
            'salary_range' => 'TZS 1.5M - 2.5M',
            'application_deadline' => now()->addDays(30),
            'closing_date' => now()->addDays(32),
            'responsibilities' => "Develop web apps",
            'qualifications' => 'BSc Computer Science',
            'required_experience' => '3 years Laravel',
            'required_skills' => 'PHP, Laravel',
            'benefits' => 'Health insurance',
            'status' => 'Published',
        ]);
    }

    public function test_careers_tracking_page_is_accessible()
    {
        $response = $this->get('/careers/track-application');
        $response->assertStatus(200)
            ->assertSee('Track & Continue Job Application', false);
    }

    public function test_can_track_and_resume_job_application_by_phone()
    {
        // 1. Create a draft job application
        $jobApp = JobApplication::create([
            'application_number' => 'SUPA-JOB-2026-888888',
            'user_id' => $this->applicantUser->id,
            'vacancy_id' => $this->vacancy->id,
            'status' => 'Draft',
            'current_step' => 3,
            'full_name' => 'Jane Doe',
            'phone' => '0789654321',
            'whatsapp_number' => '0789654321',
            'email' => 'jane@example.com',
            'region' => 'Singida',
            'district' => 'Singida Municipal',
            'physical_address' => 'Mitundu Ward',
        ]);

        // 2. Track application via POST
        $response = $this->postJson('/api/v1/public/careers/track-application', [
            'application_number' => '0789654321', // track by phone
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('application_number', 'SUPA-JOB-2026-888888')
            ->assertJsonPath('status', 'Draft')
            ->assertJsonPath('current_step', 3);

        // 3. Resume application directly
        $resumeResponse = $this->postJson('/api/v1/public/careers/resume-direct', [
            'application_id' => $jobApp->id,
            'user_id' => $this->applicantUser->id,
        ]);

        $resumeResponse->assertStatus(200)
            ->assertJsonStructure(['redirect_url']);

        // Assert user is authenticated
        $this->assertEquals($this->applicantUser->id, Auth::id());
    }

    public function test_can_track_job_application_by_application_number()
    {
        $jobApp = JobApplication::create([
            'application_number' => 'SUPA-JOB-2026-999999',
            'user_id' => $this->applicantUser->id,
            'vacancy_id' => $this->vacancy->id,
            'status' => 'Submitted',
            'current_step' => 9,
            'full_name' => 'Jane Doe',
            'phone' => '0789654321',
            'whatsapp_number' => '0789654321',
            'email' => 'jane@example.com',
            'region' => 'Singida',
            'district' => 'Singida Municipal',
            'physical_address' => 'Mitundu Ward',
        ]);

        $response = $this->postJson('/api/v1/public/careers/track-application', [
            'application_number' => 'SUPA-JOB-2026-999999',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('application_number', 'SUPA-JOB-2026-999999')
            ->assertJsonPath('status', 'Submitted');
    }
}
