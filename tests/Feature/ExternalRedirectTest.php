<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\CareerProfile;
use App\Models\JobCategory;
use App\Models\Designation;
use App\Models\Position;
use App\Models\Vacancy;
use App\Models\JobApplicationIntent;
use App\Models\ExternalApplicationRedirect;
use App\Models\AuditLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class ExternalRedirectTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $vacancy;
    protected $designation;
    protected $position;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $applicantRole = Role::where('name', 'applicant')->first();
        $this->user = User::factory()->create(['role' => 'user']);
        $this->user->roles()->attach($applicantRole);

        $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $activeTerms = \App\Models\TermsCondition::where('status', 'Published')->latest('effective_date')->first();

        \App\Models\Applicant::create([
            'user_id' => $this->user->id,
            'consent_status' => 'accepted',
            'consent_given' => true,
            'privacy_policy_version' => $activePolicy ? $activePolicy->version : null,
            'terms_version' => $activeTerms ? $activeTerms->version : null,
            'initial_consent_given' => true,
            'initial_consent_version' => $activePolicy ? $activePolicy->version : null,
        ]);

        $this->category = JobCategory::first();
        $this->designation = Designation::first();
        $this->position = Position::first();

        $this->vacancy = Vacancy::create([
            'vacancy_number' => 'VAC-TEST-12345',
            'job_title' => 'Software Instructor',
            'designation_id' => $this->designation->id,
            'position_id' => $this->position->id,
            'job_category_id' => $this->category->id,
            'number_of_positions' => 1,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida',
            'application_deadline' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'Published',
            'responsibilities' => 'Teaching software development',
            'qualifications' => 'BSc CS',
            'required_experience' => '3 years',
            'required_skills' => 'Laravel, PHP',
            'application_type' => 'external',
            'external_url' => 'https://ajiramarket.co.tz/jobs/software-instructor',
            'external_provider' => 'ajiramarket',
        ]);
    }

    public function test_guest_is_redirected_to_login()
    {
        $response = $this->get(route('careers.vacancy.confirm', $this->vacancy->vacancy_number));
        $response->assertRedirect(route('login'));
    }

    public function test_missing_profile_blocks_redirect_and_redirects_to_profile_create()
    {
        $response = $this->actingAs($this->user)->get(route('careers.vacancy.confirm', $this->vacancy->vacancy_number));
        $response->assertRedirect(route('career.profile.create'));
    }

    public function test_authenticated_user_with_profile_can_access_confirm_page()
    {
        CareerProfile::create([
            'user_id' => $this->user->id,
            'current_profession' => 'Instructor',
            'years_experience' => 3,
            'skills' => ['PHP'],
            'cv_path' => 'private/cv/cv.pdf',
            'preferred_job_categories' => ['Teaching'],
            'preferred_locations' => ['Singida'],
            'expected_salary' => 1000000,
            'availability_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->user)->get(route('careers.vacancy.confirm', $this->vacancy->vacancy_number));
        $response->assertStatus(200);
        $response->assertSee('External Application Redirect');
        $response->assertSee($this->vacancy->job_title);
    }

    public function test_redirect_without_valid_signature_is_blocked()
    {
        CareerProfile::create([
            'user_id' => $this->user->id,
            'current_profession' => 'Instructor',
            'years_experience' => 3,
            'skills' => ['PHP'],
            'cv_path' => 'private/cv/cv.pdf',
            'preferred_job_categories' => ['Teaching'],
            'preferred_locations' => ['Singida'],
            'expected_salary' => 1000000,
            'availability_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->user)->get(route('careers.vacancy.redirect', $this->vacancy->vacancy_number));
        $response->assertStatus(403);
    }

    public function test_authenticated_user_with_profile_can_start_redirect()
    {
        CareerProfile::create([
            'user_id' => $this->user->id,
            'current_profession' => 'Instructor',
            'years_experience' => 3,
            'skills' => ['PHP'],
            'cv_path' => 'private/cv/cv.pdf',
            'preferred_job_categories' => ['Teaching'],
            'preferred_locations' => ['Singida'],
            'expected_salary' => 1000000,
            'availability_date' => now()->format('Y-m-d'),
        ]);

        $signedUrl = URL::temporarySignedRoute(
            'careers.vacancy.redirect',
            now()->addMinutes(10),
            ['vacancy_number' => $this->vacancy->vacancy_number]
        );

        $response = $this->actingAs($this->user)->get($signedUrl);

        $response->assertRedirect();
        $targetUrl = $response->headers->get('Location');
        $this->assertStringContainsString('https://ajiramarket.co.tz/jobs/software-instructor', $targetUrl);
        $this->assertStringContainsString('source=supa', $targetUrl);
        $this->assertStringContainsString('ref=', $targetUrl);

        $this->assertDatabaseHas('job_application_intents', [
            'user_id' => $this->user->id,
            'vacancy_id' => $this->vacancy->id,
            'status' => 'redirected',
        ]);

        $redirect = ExternalApplicationRedirect::where('user_id', $this->user->id)->first();
        $this->assertNotNull($redirect);
        $this->assertEquals(64, strlen($redirect->tracking_token));

        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->user->id,
            'action' => 'external_job_redirect',
        ]);
        $this->assertDatabaseHas('audit_logs', [
            'user_id' => $this->user->id,
            'action' => 'job_application_intent_created',
        ]);
    }
}
