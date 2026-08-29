<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\CareerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CareerProfileTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $otherUser;
    protected $hrUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $applicantRole = Role::where('name', 'applicant')->first();
        
        $this->user = User::factory()->create(['role' => 'user']);
        $this->user->roles()->attach($applicantRole);

        $this->otherUser = User::factory()->create(['role' => 'user']);
        $this->otherUser->roles()->attach($applicantRole);

        $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $activeTerms = \App\Models\TermsCondition::where('status', 'Published')->latest('effective_date')->first();

        foreach ([$this->user, $this->otherUser] as $u) {
            \App\Models\Applicant::create([
                'user_id' => $u->id,
                'consent_status' => 'accepted',
                'consent_given' => true,
                'privacy_policy_version' => $activePolicy ? $activePolicy->version : null,
                'terms_version' => $activeTerms ? $activeTerms->version : null,
                'initial_consent_given' => true,
                'initial_consent_version' => $activePolicy ? $activePolicy->version : null,
            ]);
        }

        $hrRole = Role::where('name', 'hr_manager')->first();
        $this->hrUser = User::factory()->create(['role' => 'registrar']); // staff role
        $this->hrUser->roles()->attach($hrRole);
    }

    public function test_authenticated_user_can_view_profile_creation_wizard()
    {
        $response = $this->actingAs($this->user)->get(route('career.profile.create'));
        $response->assertStatus(200);
    }

    public function test_candidate_can_create_career_profile_with_valid_data_and_cv()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('my_cv.pdf', 100, 'application/pdf');

        $data = [
            'current_profession' => 'Web Developer',
            'years_experience' => 3,
            'skills' => ['PHP', 'Laravel', 'Vue'],
            'linkedin_url' => 'https://linkedin.com/in/myuser',
            'portfolio_url' => 'https://myportfolio.com',
            'cv_file' => $file,
            'preferred_job_categories' => ['ICT', 'Engineering'],
            'preferred_locations' => ['Dar es Salaam', 'Dodoma'],
            'expected_salary' => 1500000,
            'availability_date' => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)->post(route('career.profile.store'), $data);
        $response->assertRedirect(route('career.profile.show'));

        $this->assertDatabaseHas('career_profiles', [
            'user_id' => $this->user->id,
            'current_profession' => 'Web Developer',
            'years_experience' => 3,
            'expected_salary' => 1500000,
        ]);

        $profile = CareerProfile::where('user_id', $this->user->id)->first();
        $this->assertNotNull($profile->cv_path);
        Storage::assertExists($profile->cv_path);
    }

    public function test_candidate_can_update_their_career_profile()
    {
        Storage::fake('local');
        $oldFile = UploadedFile::fake()->create('old_cv.pdf', 100, 'application/pdf');
        $profile = CareerProfile::create([
            'user_id' => $this->user->id,
            'current_profession' => 'Junior Dev',
            'years_experience' => 1,
            'skills' => ['HTML', 'CSS'],
            'cv_path' => $oldFile->store('private/cv'),
            'preferred_job_categories' => ['ICT'],
            'preferred_locations' => ['Singida'],
            'expected_salary' => 800000,
            'availability_date' => now()->format('Y-m-d'),
        ]);

        $newFile = UploadedFile::fake()->create('new_cv.pdf', 200, 'application/pdf');
        $data = [
            'current_profession' => 'Senior Developer',
            'years_experience' => 5,
            'skills' => ['PHP', 'Laravel', 'Docker'],
            'cv_file' => $newFile,
            'preferred_job_categories' => ['Management', 'ICT'],
            'preferred_locations' => ['Dar es Salaam'],
            'expected_salary' => 3000000,
            'availability_date' => now()->addDays(15)->format('Y-m-d'),
        ];

        $response = $this->actingAs($this->user)->put(route('career.profile.update'), $data);
        $response->assertRedirect(route('career.profile.show'));

        $this->assertDatabaseHas('career_profiles', [
            'id' => $profile->id,
            'current_profession' => 'Senior Developer',
            'years_experience' => 5,
            'expected_salary' => 3000000,
        ]);

        Storage::assertMissing($profile->cv_path);
        $updatedProfile = CareerProfile::find($profile->id);
        Storage::assertExists($updatedProfile->cv_path);
    }

    public function test_unauthorized_user_cannot_view_or_edit_other_profiles()
    {
        Storage::fake('local');
        $cvFile = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');
        $profile = CareerProfile::create([
            'user_id' => $this->user->id,
            'current_profession' => 'Junior Dev',
            'years_experience' => 1,
            'skills' => ['HTML', 'CSS'],
            'cv_path' => $cvFile->store('private/cv'),
            'preferred_job_categories' => ['ICT'],
            'preferred_locations' => ['Singida'],
            'expected_salary' => 800000,
            'availability_date' => now()->format('Y-m-d'),
        ]);

        // Other candidate cannot view or edit
        $response = $this->actingAs($this->otherUser)->get(route('career.profile.show'));
        $response->assertRedirect(route('career.profile.create'));

        $this->assertFalse($this->otherUser->can('update', $profile));
        $this->assertTrue($this->user->can('update', $profile));
    }

    public function test_staff_can_view_candidate_cv_but_cannot_modify()
    {
        Storage::fake('local');
        $cvFile = UploadedFile::fake()->create('cv.pdf', 100, 'application/pdf');
        $profile = CareerProfile::create([
            'user_id' => $this->user->id,
            'current_profession' => 'Junior Dev',
            'years_experience' => 1,
            'skills' => ['HTML', 'CSS'],
            'cv_path' => $cvFile->store('private/cv'),
            'preferred_job_categories' => ['ICT'],
            'preferred_locations' => ['Singida'],
            'expected_salary' => 800000,
            'availability_date' => now()->format('Y-m-d'),
        ]);

        $this->assertTrue($this->hrUser->can('downloadCv', $profile));
        $this->assertFalse($this->hrUser->can('update', $profile));
    }
}
