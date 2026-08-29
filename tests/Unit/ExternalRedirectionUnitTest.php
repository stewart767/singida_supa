<?php

namespace Tests\Unit;

use App\Services\ExternalApplicationService;
use App\Repositories\Contracts\CareerProfileRepositoryInterface;
use App\Repositories\Contracts\ExternalApplicationRedirectRepositoryInterface;
use App\Repositories\Contracts\JobApplicationIntentRepositoryInterface;
use App\Models\Vacancy;
use App\Models\User;
use App\Models\CareerProfile;
use App\Models\JobApplicationIntent;
use App\Models\ExternalApplicationRedirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExternalRedirectionUnitTest extends TestCase
{
    use RefreshDatabase;

    protected $service;
    protected $profileRepo;
    protected $redirectRepo;
    protected $intentRepo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->profileRepo = resolve(CareerProfileRepositoryInterface::class);
        $this->redirectRepo = resolve(ExternalApplicationRedirectRepositoryInterface::class);
        $this->intentRepo = resolve(JobApplicationIntentRepositoryInterface::class);

        $this->service = new ExternalApplicationService(
            $this->profileRepo,
            $this->redirectRepo,
            $this->intentRepo
        );
    }

    public function test_token_generation_uniqueness()
    {
        $tokens = [];
        for ($i = 0; $i < 100; $i++) {
            $token = hash('sha256', Str::uuid());
            $this->assertEquals(64, strlen($token));
            $this->assertNotContains($token, $tokens);
            $tokens[] = $token;
        }
    }

    public function test_redirect_url_builder()
    {
        $baseUrl = 'https://ajiramarket.co.tz/jobs/senior-dev';
        $token = 'secure-token-1234567890';
        
        $builtUrl = $this->service->buildRedirectUrl($baseUrl, $token);
        
        $this->assertStringContainsString('source=supa', $builtUrl);
        $this->assertStringContainsString('ref=secure-token-1234567890', $builtUrl);
        $this->assertEquals('https://ajiramarket.co.tz/jobs/senior-dev?source=supa&ref=secure-token-1234567890', $builtUrl);

        // Test with existing query params
        $baseUrlWithQuery = 'https://ajiramarket.co.tz/jobs/senior-dev?category=it&sort=new';
        $builtUrlWithQuery = $this->service->buildRedirectUrl($baseUrlWithQuery, $token);
        $this->assertStringContainsString('category=it', $builtUrlWithQuery);
        $this->assertStringContainsString('sort=new', $builtUrlWithQuery);
        $this->assertStringContainsString('source=supa', $builtUrlWithQuery);
        $this->assertStringContainsString('ref=secure-token-1234567890', $builtUrlWithQuery);
    }

    public function test_repository_methods_and_service_business_rules()
    {
        $user = User::factory()->create();

        // 1. CareerProfile Repository and model logic
        $profile = $this->profileRepo->create([
            'user_id' => $user->id,
            'current_profession' => 'QA Specialist',
            'years_experience' => 4,
            'skills' => ['PHPUnit', 'Selenium'],
            'cv_path' => 'private/cv/cv.pdf',
            'preferred_job_categories' => ['ICT'],
            'preferred_locations' => ['Dar es Salaam'],
            'expected_salary' => 2000000,
            'availability_date' => now()->format('Y-m-d'),
        ]);

        $foundProfile = $this->profileRepo->findForUser($user->id);
        $this->assertNotNull($foundProfile);
        $this->assertEquals('QA Specialist', $foundProfile->current_profession);

        // Update
        $this->profileRepo->update($profile->id, ['current_profession' => 'Lead QA']);
        $this->assertEquals('Lead QA', $profile->fresh()->current_profession);

        // 2. Intent and Redirect Repositories
        $vacancy = Vacancy::create([
            'vacancy_number' => 'VAC-UNIT-777',
            'job_title' => 'QA Lead',
            'designation_id' => 1,
            'position_id' => 1,
            'job_category_id' => 1,
            'number_of_positions' => 1,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Dodoma',
            'application_deadline' => now()->addDays(5)->format('Y-m-d'),
            'status' => 'Published',
            'responsibilities' => 'Testing',
            'qualifications' => 'BSc',
            'required_experience' => '3 years',
            'required_skills' => 'QA',
            'application_type' => 'external',
            'external_url' => 'https://ajiramarket.co.tz/jobs/qa-lead',
        ]);

        $redirectUrl = $this->service->initiateRedirect($user, $vacancy);
        $this->assertNotNull($redirectUrl);

        $intent = $this->intentRepo->findForUserVacancy($user->id, $vacancy->id);
        $this->assertNotNull($intent);
        $this->assertEquals('redirected', $intent->status);

        $redirect = ExternalApplicationRedirect::where('user_id', $user->id)->where('vacancy_id', $vacancy->id)->first();
        $this->assertNotNull($redirect);
        $this->assertEquals($vacancy->external_url, $redirect->destination_url);
    }

    public function test_factories_creation()
    {
        $profile = CareerProfile::factory()->create();
        $this->assertNotNull($profile);
        $this->assertInstanceOf(CareerProfile::class, $profile);

        $redirect = ExternalApplicationRedirect::factory()->create();
        $this->assertNotNull($redirect);
        $this->assertInstanceOf(ExternalApplicationRedirect::class, $redirect);

        $intent = JobApplicationIntent::factory()->create();
        $this->assertNotNull($intent);
        $this->assertInstanceOf(JobApplicationIntent::class, $intent);
    }
}
