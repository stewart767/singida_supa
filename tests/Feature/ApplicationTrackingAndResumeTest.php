<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Intake;
use App\Models\Programme;
use App\Models\User;
use App\Models\ApplicationActivity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ApplicationTrackingAndResumeTest extends TestCase
{
    use RefreshDatabase;

    protected User $guestUser;
    protected AcademicYear $academicYear;
    protected Intake $intake;
    protected Programme $programme;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed basic dependencies
        $this->academicYear = AcademicYear::create([
            'code' => '2026/2027',
            'name' => '2026/2027 Academic Year',
            'start_date' => '2026-09-01',
            'end_date' => '2027-06-30',
            'application_deadline' => '2026-10-31',
            'is_current' => true,
            'is_active' => true,
        ]);

        $this->intake = Intake::create([
            'name' => 'September Intake',
            'code' => 'SEP2026',
            'description' => 'Main Academic Intake',
            'is_active' => true,
        ]);

        $this->programme = Programme::create([
            'code' => 'BSCED',
            'name' => 'Bachelor of Science with Education',
            'duration_years' => 3,
            'annual_fee' => 1200000,
            'is_active' => true,
        ]);

        // Create guest user
        $this->guestUser = User::create([
            'name' => 'Guest User',
            'email' => 'guest@supa-guest.com',
            'phone' => '0712345678',
            'role' => 'APPLICANT',
            'password' => bcrypt('password123'),
            'is_active' => true,
        ]);

        Applicant::create([
            'user_id' => $this->guestUser->id,
            'nationality' => 'Tanzanian',
        ]);
    }

    public function test_guest_credentials_initializes_draft_application()
    {
        $this->actingAs($this->guestUser);

        // Put guest session identifier to pass middleware check
        session(['guest_user_id' => $this->guestUser->id]);

        $response = $this->postJson('/applicant/guest-credentials', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '0712345678',
            'whatsapp_number' => '0712345678',
            'consent_given' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('user.name', 'John Doe')
            ->assertJsonStructure(['application']);

        // Check application exists in DB
        $app = Application::first();
        $this->assertNotNull($app);
        $this->assertEquals('Draft', $app->status);
        $this->assertEquals(1, $app->current_step);
        $this->assertEquals(14, $app->completion_percentage);

        // Check activity log exists
        $this->assertTrue(ApplicationActivity::where('application_id', $app->id)
            ->where('action', 'Application Started')
            ->exists());
    }

    public function test_personal_info_saves_step_progression()
    {
        $this->actingAs($this->guestUser);
        session(['guest_user_id' => $this->guestUser->id]);

        // Create initial application draft
        $app = Application::create([
            'applicant_id' => $this->guestUser->applicant->id,
            'application_number' => 'SUPA-2026-000001',
            'status' => 'Draft',
            'current_step' => 1,
            'completion_percentage' => 14,
        ]);

        $response = $this->postJson('/applicant/personal-info', [
            'gender' => 'male',
            'date_of_birth' => '2000-01-01',
            'nida_number' => '20000101-12345-00001-20',
            'region' => 'Singida',
            'district' => 'Singida Municipal',
            'ward' => 'Mitundu',
            'next_of_kin_name' => 'Parent Kin',
            'next_of_kin_phone' => '0712222333',
            'next_of_kin_relation' => 'Father',
        ]);

        $response->assertStatus(200);

        $app->refresh();
        $this->assertEquals(3, $app->current_step);
        $this->assertEquals(28, $app->completion_percentage);

        $this->assertTrue(ApplicationActivity::where('application_id', $app->id)
            ->where('action', 'Step 2 Completed')
            ->exists());
    }

    public function test_otp_tracking_and_resume_flow()
    {
        // Setup application
        $app = Application::create([
            'applicant_id' => $this->guestUser->applicant->id,
            'application_number' => 'SUPA-2026-000001',
            'status' => 'Draft',
            'current_step' => 3,
            'completion_percentage' => 28,
        ]);

        // 1. Track status
        $response = $this->postJson('/api/v1/public/track-application', [
            'application_number' => '0712345678', // search by phone
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('application_number', 'SUPA-2026-000001')
            ->assertJsonPath('current_step', 3);

        // 2. Request OTP
        $sendResponse = $this->postJson('/api/v1/public/resume-otp/send', [
            'application_id' => $app->id,
            'user_id' => $this->guestUser->id,
        ]);

        $sendResponse->assertStatus(200);

        $this->guestUser->refresh();
        $this->assertNotNull($this->guestUser->otp_code);

        // 3. Verify OTP
        $verifyResponse = $this->postJson('/api/v1/public/resume-otp/verify', [
            'application_id' => $app->id,
            'user_id' => $this->guestUser->id,
            'otp_code' => $this->guestUser->otp_code,
        ]);

        $verifyResponse->assertStatus(200)
            ->assertJsonStructure(['redirect_url']);

        // Assert user is authenticated
        $this->assertEquals($this->guestUser->id, Auth::id());
    }

    public function test_direct_tracking_and_resume_flow()
    {
        // Setup application
        $app = Application::create([
            'applicant_id' => $this->guestUser->applicant->id,
            'application_number' => 'SUPA-2026-000001',
            'status' => 'Draft',
            'current_step' => 3,
            'completion_percentage' => 28,
        ]);

        // 1. Track status
        $response = $this->postJson('/api/v1/public/track-application', [
            'application_number' => '0712345678', // search by phone
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('application_number', 'SUPA-2026-000001')
            ->assertJsonPath('current_step', 3);

        // 2. Resume directly
        $resumeResponse = $this->postJson('/api/v1/public/resume-direct', [
            'application_id' => $app->id,
            'user_id' => $this->guestUser->id,
        ]);

        $resumeResponse->assertStatus(200)
            ->assertJsonStructure(['redirect_url']);

        // Assert user is authenticated
        $this->assertEquals($this->guestUser->id, Auth::id());
    }

    public function test_artisan_command_expires_abandoned_drafts()
    {
        // Create an old draft application
        $app = Application::create([
            'applicant_id' => $this->guestUser->applicant->id,
            'application_number' => 'SUPA-2026-000001',
            'status' => 'Draft',
            'current_step' => 2,
            'completion_percentage' => 28,
            'last_activity_at' => now()->subDays(31),
            'updated_at' => now()->subDays(31),
        ]);

        // Run the expire-drafts command
        $this->artisan('applications:expire-drafts')
            ->expectsOutputToContain('Expired 1 application(s).')
            ->assertExitCode(0);

        $app->refresh();
        $this->assertEquals('EXPIRED', $app->status);
    }
}
