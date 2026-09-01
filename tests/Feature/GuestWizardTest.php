<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Intake;
use App\Models\Programme;
use App\Models\Setting;
use App\Models\User;
use App\Models\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class GuestWizardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_guest_wizard_workflow_and_claiming_account()
    {
        // 1. Disable applicant_login_required setting
        Setting::set('applicant_login_required', false, 'admission', 'boolean');

        // 2. Request the wizard page without authentication
        $response = $this->get('/applicant/apply-wizard');
        if ($response->isRedirect()) {
            $response = $this->followRedirects($response);
        }
        $response->assertStatus(200);

        // Assert a guest user is created and logged in automatically
        $user = Auth::user();
        $this->assertNotNull($user);
        $this->assertStringContainsString('@supa-guest.com', $user->email);
        $this->assertTrue(session()->has('guest_user_id'));
        $this->assertEquals($user->id, session('guest_user_id'));

        $acceptResponse = $this->post('/applicant/consent-notice/accept', [
            'confirm_accurate' => true,
            'read_privacy' => true,
            'read_terms' => true,
            'consent_given' => true,
            'understand_rights' => true,
        ]);
        $acceptResponse->assertRedirect();
        
        $user->refresh();
        $credentials = [
            'name' => 'Jane Guest',
            'email' => 'jane.doe@example.com',
            'phone' => '+255711223344',
            'consent_given' => true,
        ];

        $res1 = $this->postJson('/applicant/guest-credentials', $credentials);
        $res1->assertStatus(200);

        $user->refresh();
        $this->assertEquals('Jane Guest', $user->name);
        $this->assertEquals('jane.doe@example.com', $user->email);
        $this->assertEquals('+255711223344', $user->phone);

        // 4. Test re-submitting / editing guest credentials (should not fail on unique rules or 403)
        $updatedCredentials = [
            'name' => 'Jane Doe Guest',
            'email' => 'jane.doe@example.com',
            'phone' => '+255711223344',
            'consent_given' => true,
        ];

        $res2 = $this->postJson('/applicant/guest-credentials', $updatedCredentials);
        $res2->assertStatus(200);

        $user->refresh();
        $this->assertEquals('Jane Doe Guest', $user->name);

        // 5. Complete Step 2: Personal Info
        $this->actingAs($user);
        $this->postJson('/api/v1/applicant/personal-info', [
            'gender' => 'female',
            'date_of_birth' => '2001-05-12',
            'voter_id_number' => 'T-9999-8888-777',
            'region' => 'Singida',
            'district' => 'Singida Manispaa',
            'ward' => 'Majengo',
            'next_of_kin_name' => 'Kin Person',
            'next_of_kin_phone' => '+255755123456',
            'next_of_kin_relation' => 'Mother',
        ])->assertStatus(200);

        // 6. Complete Step 3: Academic Profile (creates the application)
        $programme = Programme::first();
        $academicYear = AcademicYear::first();
        $intake = Intake::first();

        $this->postJson('/api/v1/applicant/academic-profile', [
            'admission_type' => 'Diploma',
            'programme_id' => $programme->id,
            'academic_year_id' => $academicYear->id,
            'intake_id' => $intake->id,
            'college_name' => 'STTC',
            'diploma_programme_name' => 'Diploma in Education',
            'diploma_registration_number' => 'STTC/2022/999',
            'diploma_graduation_year' => 2024,
            'gpa' => 3.8,
        ])->assertStatus(200);

        // Assert application exists and is marked as is_public_submission => true
        $application = Application::where('applicant_id', $user->applicant->id)->first();
        $this->assertNotNull($application);
        $this->assertTrue((bool)$application->is_public_submission);

        // 7. Complete final submission
        $this->postJson('/api/v1/applicant/submit-final', [
            'digital_signature' => 'Jane Doe Guest',
            'confirm_accurate' => true,
            'read_privacy' => true,
            'read_terms' => true,
            'consent_given' => true,
            'understand_penalty' => true,
        ])->assertStatus(200);

        // 8. Claim account by setting a password
        $this->postJson('/applicant/claim-account', [
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertStatus(200);

        // Assert session guest flag is removed
        $this->assertFalse(session()->has('guest_user_id'));

        // 9. Logout
        $this->post('/logout')->assertStatus(302);
        $this->assertGuest();

        // 10. Login with new credentials
        $loginRes = $this->postJson('/login', [
            'email' => 'jane.doe@example.com',
            'password' => 'Password123!',
        ]);

        $loginRes->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_access_dashboard_without_consent()
    {
        $user = User::create([
            'name' => 'No Consent User',
            'email' => 'noconsent@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'applicant',
            'is_active' => true,
        ]);
        \App\Models\Applicant::create([
            'user_id' => $user->id,
            'initial_consent_given' => false
        ]);

        $this->actingAs($user);

        $response = $this->get('/applicant/dashboard');
        $response->assertRedirect(route('applicant.consent.notice'));
    }

    public function test_user_cannot_bypass_wizard_steps_without_consent()
    {
        $user = User::create([
            'name' => 'No Consent User 2',
            'email' => 'noconsent2@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'applicant',
            'is_active' => true,
        ]);
        \App\Models\Applicant::create([
            'user_id' => $user->id,
            'initial_consent_given' => false
        ]);

        $this->actingAs($user);

        // Try to access step 3 directly
        $response = $this->get('/applicant/apply-wizard?step=3');
        $response->assertRedirect(route('applicant.consent.notice'));
    }

    public function test_api_endpoints_blocked_without_consent()
    {
        $user = User::create([
            'name' => 'No Consent User 3',
            'email' => 'noconsent3@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'applicant',
            'is_active' => true,
        ]);
        \App\Models\Applicant::create([
            'user_id' => $user->id,
            'initial_consent_given' => false
        ]);

        $this->actingAs($user);

        // Try to fetch current application state (which now calls checkInitialConsent())
        $this->getJson('/api/v1/applicant/application')
             ->assertStatus(403);

        // Try to POST personal info
        $this->postJson('/api/v1/applicant/personal-info', [
            'gender' => 'male',
            'date_of_birth' => '2000-01-01',
            'nida_number' => '20000101123450000112',
            'region' => 'Singida',
            'district' => 'Singida',
            'ward' => 'Majengo',
            'next_of_kin_name' => 'Kin Person',
            'next_of_kin_phone' => '+255711223344',
            'next_of_kin_relation' => 'Father'
        ])->assertStatus(403);
    }

    public function test_admin_can_upload_consent_document_and_user_can_view_it()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        // 1. Create an admin user and log in
        $admin = User::create([
            'name' => 'Admin Compliance',
            'email' => 'admin.compliance@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        // 2. Submit a Privacy Policy with an uploaded file
        $file = \Illuminate\Http\UploadedFile::fake()->create('consent_written.pdf', 500, 'application/pdf');

        $response = $this->post('/admin/compliance/privacy', [
            'version' => '3.0',
            'title' => 'Written University Consent Form',
            'consent_file' => $file,
            'effective_date' => now()->toDateString(),
            'language' => 'en',
        ]);

        $response->assertRedirect();
        
        $policy = \App\Models\PrivacyPolicy::where('version', '3.0')->first();
        $this->assertNotNull($policy);
        $this->assertNotNull($policy->file_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($policy->file_path);

        // 3. Publish the policy
        $this->post("/admin/compliance/privacy/publish/{$policy->id}")->assertRedirect();
        $policy->refresh();
        $this->assertEquals('Published', $policy->status);

        // 4. Create an applicant and view the wizard page
        $applicantUser = User::create([
            'name' => 'New Applicant',
            'email' => 'newapplicant@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'applicant',
            'is_active' => true,
        ]);
        \App\Models\Applicant::create([
            'user_id' => $applicantUser->id,
            'initial_consent_given' => false
        ]);

        $this->actingAs($applicantUser);

        $response = $this->get('/applicant/consent-notice');
        $response->assertStatus(200);
        $response->assertSee('Written University Consent Form');
        $response->assertSee('3.0');
    }

    public function test_admin_can_delete_compliance_documents()
    {
        \Illuminate\Support\Facades\Storage::fake('public');

        $admin = User::create([
            'name' => 'Admin Compliance',
            'email' => 'admin.compliance2@example.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password123'),
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        $this->actingAs($admin);

        // Submit a Privacy Policy with an uploaded file
        $file = \Illuminate\Http\UploadedFile::fake()->create('consent_written.pdf', 500, 'application/pdf');

        $this->post('/admin/compliance/privacy', [
            'version' => '4.0',
            'title' => 'Written University Consent Form v4',
            'consent_file' => $file,
            'effective_date' => now()->toDateString(),
            'language' => 'en',
        ])->assertRedirect();
        
        $policy = \App\Models\PrivacyPolicy::where('version', '4.0')->first();
        $this->assertNotNull($policy);
        $this->assertNotNull($policy->file_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($policy->file_path);

        // Delete the privacy policy
        $this->delete("/admin/compliance/privacy/{$policy->id}")->assertRedirect();

        // Assert the policy is deleted from the DB
        $this->assertNull(\App\Models\PrivacyPolicy::find($policy->id));
        // Assert the attached file is deleted from storage
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($policy->file_path);

        // Submit a Terms & Conditions draft
        $termsFile = \Illuminate\Http\UploadedFile::fake()->create('terms_written.pdf', 500, 'application/pdf');

        $this->post('/admin/compliance/terms', [
            'version' => '4.0',
            'title' => 'Terms Form v4',
            'consent_file' => $termsFile,
            'effective_date' => now()->toDateString(),
            'language' => 'en',
        ])->assertRedirect();

        $terms = \App\Models\TermsCondition::where('version', '4.0')->first();
        $this->assertNotNull($terms);
        $this->assertNotNull($terms->file_path);
        \Illuminate\Support\Facades\Storage::disk('public')->assertExists($terms->file_path);

        // Delete the terms
        $this->delete("/admin/compliance/terms/{$terms->id}")->assertRedirect();

        // Assert the terms are deleted from the DB
        $this->assertNull(\App\Models\TermsCondition::find($terms->id));
        // Assert the attached file is deleted from storage
        \Illuminate\Support\Facades\Storage::disk('public')->assertMissing($terms->file_path);
    }
}
