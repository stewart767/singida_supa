<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\JobCategory;
use App\Models\Designation;
use App\Models\Position;
use App\Models\Vacancy;
use App\Models\JobApplication;
use App\Models\Interview;
use App\Models\WrittenTest;
use App\Models\OfferLetter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecruitmentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    protected $hrManager;
    protected $applicant;
    protected $designationHead;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // Retrieve seeded roles and create test users
        $managerRole = Role::where('name', 'hr_manager')->first();
        $this->hrManager = User::factory()->create();
        $this->hrManager->roles()->attach($managerRole);

        $applicantRole = Role::where('name', 'applicant')->first();
        $this->applicant = User::factory()->create(['ajira_linked' => true]);
        $this->applicant->roles()->attach($applicantRole);

        $activePolicy = \App\Models\PrivacyPolicy::where('status', 'Published')->latest('effective_date')->first();
        $activeTerms = \App\Models\TermsCondition::where('status', 'Published')->latest('effective_date')->first();

        \App\Models\Applicant::create([
            'user_id' => $this->applicant->id,
            'consent_status' => 'accepted',
            'consent_given' => true,
            'privacy_policy_version' => $activePolicy ? $activePolicy->version : null,
            'terms_version' => $activeTerms ? $activeTerms->version : null,
            'initial_consent_given' => true,
            'initial_consent_version' => $activePolicy ? $activePolicy->version : null,
        ]);
        
        $desigHeadRole = Role::where('name', 'designation_head')->first();
        $this->designationHead = User::factory()->create();
        $this->designationHead->roles()->attach($desigHeadRole);
    }

    public function test_full_recruitment_lifecycle()
    {
        Storage::fake('public');

        // 1. Log in as HR Manager and create a Job Category, Designation, Position
        $this->actingAs($this->hrManager);

        $category = JobCategory::create([
            'name' => 'IT & Engineering',
            'description' => 'Software and infrastructure roles',
            'status' => 'active',
            'display_order' => 1
        ]);

        $designation = Designation::create([
            'name' => 'Information Technology',
            'short_code' => 'IT',
            'head_of_designation_id' => $this->designationHead->id,
            'description' => 'IT desig',
            'status' => 'active'
        ]);

        $position = Position::create([
            'name' => 'Senior Laravel Developer',
            'designation_id' => $designation->id,
            'job_category_id' => $category->id,
            'employment_type' => 'Full-time',
            'salary_grade' => 'PG 8',
            'status' => 'active'
        ]);

        // 2. Create Vacancy
        $vacancyData = [
            'job_title' => 'Senior Laravel Developer',
            'department_name' => 'IT Department',
            'designation_id' => $designation->id,
            'position_id' => $position->id,
            'job_category_id' => $category->id,
            'number_of_positions' => 2,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida',
            'recommended_region' => 'Singida',
            'salary_range' => 'TZS 2.5M - 3.5M',
            'application_deadline' => now()->addDays(10)->format('Y-m-d'),
            'closing_date' => now()->addDays(12)->format('Y-m-d'),
            'responsibilities' => "Develop web applications\nMaintain systems",
            'qualifications' => 'Bachelor in Computer Science',
            'required_experience' => '5 years Laravel',
            'required_skills' => 'PHP, Laravel, Tailwind CSS, Alpine.js',
            'benefits' => 'Health insurance, Transport allowance',
            'status' => 'Published'
        ];

        $response = $this->post(route('admin.recruitment.vacancies.store'), $vacancyData);
        $response->assertRedirect();
        
        $vacancy = Vacancy::orderBy('id', 'desc')->first();
        $this->assertNotNull($vacancy);
        $this->assertEquals('Senior Laravel Developer', $vacancy->job_title);
        $this->assertEquals('Published', $vacancy->status);

        // 3. View Public Careers Page and vacancy details
        $this->actingAs($this->applicant);

        $response = $this->get(route('public.careers.index'));
        $response->assertStatus(200);
        $response->assertSee('Senior Laravel Developer');

        $response = $this->get(route('public.careers.show', $vacancy->vacancy_number));
        $response->assertStatus(200);
        $response->assertSee('5 years Laravel');

        // 4. Submit Job Application using wizard flow
        $cv = UploadedFile::fake()->create('resume.pdf', 100);
        
        $applicationPayload = [
            'vacancy_id' => $vacancy->id,
            'full_name' => 'Test Applicant',
            'email' => 'applicant@example.com',
            'phone' => '+255711223344',
            'gender' => 'male',
            'date_of_birth' => '1998-05-15',
            'nida_number' => '19980515123456789012',
            'region' => 'Singida',
            'district' => 'Singida Mjini',
            'ward' => 'Central',
            'education' => [
                [
                    'institution' => 'University of Dar es Salaam',
                    'level' => 'Degree',
                    'field' => 'Computer Science',
                    'start_year' => '2017',
                    'end_year' => '2020'
                ]
            ],
            'experience' => [
                [
                    'company' => 'Tech Solutions Ltd',
                    'position' => 'Junior Developer',
                    'start_date' => '2020-09-01',
                    'end_date' => '2023-08-31',
                    'description' => 'Wrote PHP and JS'
                ]
            ],
            'skills' => ['PHP', 'Laravel', 'Vue.js'],
            'references' => [
                [
                    'name' => 'Sarah Connor',
                    'phone' => '+255799887766',
                    'email' => 'sarah@example.com',
                    'organization' => 'Tech Solutions Ltd'
                ]
            ],
            'expected_salary' => 'TZS 3M',
            'availability' => 'Immediate',
            'cv_file' => $cv
        ];

        $response = $this->post(route('public.careers.submit'), $applicationPayload);
        $response->assertRedirect();

        $application = JobApplication::orderBy('id', 'desc')->first();
        $this->assertNotNull($application);
        $this->assertEquals('Test Applicant', $application->full_name);
        $this->assertEquals('Applied', $application->status);

        // 5. HR Manager transitions application to Shortlisted
        $this->actingAs($this->hrManager);

        $response = $this->postJson(route('admin.recruitment.applications.stage', $application->id), [
            'stage' => 'Shortlisted',
            'comments' => 'Candidate meets experience requirements'
        ]);
        $response->assertOk();
        $this->assertEquals('Shortlisted', $application->fresh()->status);

        // 6. Schedule Interview
        $interviewData = [
            'job_application_id' => $application->id,
            'type' => 'Physical',
            'date' => now()->addDays(2)->format('Y-m-d'),
            'time' => '10:00:00',
            'venue' => 'Main Boardroom',
            'panel_members' => [$this->hrManager->id, $this->designationHead->id],
            'instructions' => 'Bring original certificate copies.'
        ];

        $response = $this->post(route('admin.recruitment.interviews.schedule'), $interviewData);
        $response->assertRedirect();

        $interview = Interview::orderBy('id', 'desc')->first();
        $this->assertNotNull($interview);
        $this->assertEquals('Physical', $interview->type);
        $this->assertEquals('Interview Scheduled', $application->fresh()->status);

        // 7. Submit Scorecard
        $scorecardData = [
            'interview_id' => $interview->id,
            'communication' => 8,
            'technical_knowledge' => 9,
            'problem_solving' => 8,
            'leadership' => 7,
            'teamwork' => 8,
            'confidence' => 9,
            'professionalism' => 9,
            'comments' => 'Excellent communication and deep Laravel knowledge.'
        ];

        $response = $this->post(route('admin.recruitment.scores.store'), $scorecardData);
        $response->assertRedirect();
        
        $scorecard = $interview->scorecards()->first();
        $this->assertNotNull($scorecard);
        $this->assertEquals(58 / 7, $scorecard->average_score);

        // 8. Assign Written Test and Record Marks
        $testData = [
            'job_application_id' => $application->id,
            'test_name' => 'Backend Development Test',
            'assigned_date' => now()->addDay()->format('Y-m-d')
        ];

        $response = $this->post(route('admin.recruitment.written-tests.assign'), $testData);
        $response->assertRedirect();

        $writtenTest = WrittenTest::orderBy('id', 'desc')->first();
        $this->assertNotNull($writtenTest);
        $this->assertEquals('Backend Development Test', $writtenTest->test_name);

        $response = $this->post(route('admin.recruitment.written-tests.marks', $writtenTest->id), [
            'marks' => 88.5,
            'comments' => 'Splendid performance'
        ]);
        $response->assertRedirect();
        $this->assertEquals(88.5, $writtenTest->fresh()->marks);

        // 9. Submit Final Decision: Selected
        $response = $this->post(route('admin.recruitment.evaluations.decision', $application->id), [
            'decision' => 'Selected',
            'comments' => 'Recommend offering position'
        ]);
        $response->assertRedirect();
        $this->assertEquals('Selected', $application->fresh()->status);

        // 10. Generate Offer Letter
        $offerLetterData = [
            'job_application_id' => $application->id,
            'salary' => 'TZS 3,000,000 / month',
            'benefits' => 'Medical insurance + housing allowance',
            'reporting_date' => now()->addDays(30)->format('Y-m-d'),
            'employment_terms' => 'Probation period of 3 months. Permanent upon review.'
        ];

        $response = $this->post(route('admin.recruitment.offer-letters.generate'), $offerLetterData);
        $response->assertRedirect();

        $offerLetter = OfferLetter::orderBy('id', 'desc')->first();
        $this->assertNotNull($offerLetter);
        $this->assertEquals('Sent', $offerLetter->status);
        $this->assertEquals('Offer Letter', $application->fresh()->status);

        // 11. Applicant logs in, views Offer Letter, signs digitally
        $this->actingAs($this->applicant);

        $fakeSignature = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
        
        $response = $this->post(route('public.careers.offer-letter.sign', $offerLetter->id), [
            'signature' => $fakeSignature
        ]);
        $response->assertRedirect();
        
        $this->assertEquals('Accepted', $offerLetter->fresh()->status);
        $this->assertEquals('Hired', $application->fresh()->status);
    }

    public function test_guest_can_apply_to_vacancy_without_logging_in()
    {
        Storage::fake('public');

        $category = JobCategory::create([
            'name' => 'IT & Engineering',
            'status' => 'active',
            'display_order' => 1
        ]);

        $designation = Designation::create([
            'name' => 'Information Technology',
            'short_code' => 'IT',
            'status' => 'active'
        ]);

        $position = Position::create([
            'name' => 'Junior Laravel Developer',
            'designation_id' => $designation->id,
            'job_category_id' => $category->id,
            'employment_type' => 'Full-time',
            'status' => 'active'
        ]);

        $vacancy = Vacancy::create([
            'vacancy_number' => 'VAC-TEST-GUEST',
            'job_title' => 'Junior Laravel Developer',
            'designation_id' => $designation->id,
            'position_id' => $position->id,
            'job_category_id' => $category->id,
            'number_of_positions' => 1,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida',
            'application_deadline' => now()->addDays(5),
            'responsibilities' => 'Coding',
            'qualifications' => 'Degree',
            'required_experience' => 'None',
            'required_skills' => 'Laravel',
            'status' => 'Published'
        ]);

        $cv = UploadedFile::fake()->create('cv.pdf', 100);

        $payload = [
            'vacancy_id' => $vacancy->id,
            'full_name' => 'Guest Visitor Jane',
            'email' => 'jane.guest@example.com',
            'phone' => '+255788998877',
            'gender' => 'female',
            'date_of_birth' => '2001-02-02',
            'region' => 'Singida',
            'district' => 'Singida',
            'education' => [
                [
                    'institution' => 'College',
                    'level' => 'Degree',
                    'field' => 'CS',
                    'start_year' => '2019',
                    'end_year' => '2022'
                ]
            ],
            'skills' => ['Laravel'],
            'expected_salary' => 'TZS 1.5M',
            'availability' => 'Immediate',
            'cv_file' => $cv
        ];

        $this->assertGuest();

        $formResponse = $this->get(route('public.careers.apply', $vacancy->vacancy_number));
        $formResponse->assertStatus(200);

        $response = $this->post(route('public.careers.submit'), $payload);
        $response->assertRedirect();

        $user = \App\Models\User::where('email', 'jane.guest@example.com')->first();
        $this->assertNotNull($user);
        $this->assertGuest();

        $application = JobApplication::where('email', 'jane.guest@example.com')->first();
        $this->assertNotNull($application);
        $this->assertEquals($user->id, $application->user_id);
    }

    public function test_nine_step_wizard_flow()
    {
        Storage::fake('public');

        $category = JobCategory::create([
            'name' => 'Education',
            'status' => 'active',
        ]);

        $designation = Designation::create([
            'name' => 'Languages',
            'short_code' => 'LANG',
            'status' => 'active'
        ]);

        $position = Position::create([
            'name' => 'Tutor of English',
            'designation_id' => $designation->id,
            'job_category_id' => $category->id,
            'employment_type' => 'Full-time',
            'status' => 'active'
        ]);

        $vacancy = Vacancy::create([
            'vacancy_number' => 'VAC-TUTOR-ENG',
            'job_title' => 'Tutor of English',
            'designation_id' => $designation->id,
            'position_id' => $position->id,
            'job_category_id' => $category->id,
            'number_of_positions' => 1,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida',
            'application_deadline' => now()->addDays(5),
            'responsibilities' => 'Teaching English',
            'qualifications' => 'Degree in Education',
            'required_experience' => '2 years',
            'required_skills' => 'Teaching, English',
            'status' => 'Published'
        ]);

        $this->actingAs($this->applicant);

        // Step 1: Position Confirmation
        $response = $this->postJson(route('public.careers.apply.save-step'), [
            'step' => 1,
            'vacancy_id' => $vacancy->id
        ]);
        $response->assertOk();

        // Step 2: Personal Information Details
        $response = $this->postJson(route('public.careers.apply.save-step'), [
            'step' => 2,
            'vacancy_id' => $vacancy->id,
            'full_name' => 'STTC Teacher Candidate',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01',
            'nida_number' => '19900101123456789012',
            'phone' => '+255755123456',
            'whatsapp_number' => '+255755123456',
            'email' => 'tutor.candidate@example.com',
            'region' => 'Singida',
            'district' => 'Singida Mjini',
            'physical_address' => 'STTC Campus Housing'
        ]);
        $response->assertOk();
        
        $appId = $response->json('application_id');
        $this->assertNotNull($appId);

        $application = JobApplication::find($appId);
        $this->assertEquals('STTC Teacher Candidate', $application->full_name);
        $this->assertEquals(2, $application->current_step);

        // Step 3: Employment Experience
        $response = $this->postJson(route('public.careers.apply.save-step'), [
            'step' => 3,
            'vacancy_id' => $vacancy->id,
            'application_id' => $appId,
            'worked_at_sttc' => '1',
            'sttc_experience' => [
                'campus' => 'Singida Campus',
                'department' => 'Languages',
                'position_held' => 'Assistant Tutor',
                'start_year' => '2022',
                'end_year' => '2024',
                'reason_for_leaving' => 'End of contract'
            ],
            'experience_history' => [
                [
                    'employer' => 'Singida Secondary School',
                    'position' => 'Teacher',
                    'start_year' => '2020',
                    'end_year' => '2022',
                    'employment_type' => 'Contract',
                    'responsibilities' => 'Taught O-Level English classes'
                ]
            ]
        ]);
        $response->assertOk();

        // Step 4: Education History
        $response = $this->postJson(route('public.careers.apply.save-step'), [
            'step' => 4,
            'vacancy_id' => $vacancy->id,
            'application_id' => $appId,
            'education_history' => [
                [
                    'institution' => 'University of Dodoma',
                    'level' => 'Bachelor',
                    'award' => 'Bachelor of Arts with Education',
                    'programme' => 'English Language and Literature',
                    'start_year' => '2016',
                    'completion_year' => '2019',
                    'gpa_grade' => '3.8',
                    'certificate_path' => 'uploaded'
                ]
            ]
        ]);
        $response->assertOk();

        // Step 5: ICT Competency
        $response = $this->postJson(route('public.careers.apply.save-step'), [
            'step' => 5,
            'vacancy_id' => $vacancy->id,
            'application_id' => $appId,
            'ict_description' => 'I use LMS and PowerPoint for English lessons.',
            'ict_skills' => [
                ['skill' => 'Microsoft Word', 'level' => 'Advanced'],
                ['skill' => 'LMS', 'level' => 'Intermediate']
            ]
        ]);
        $response->assertOk();

        // Step 6: Professional Qualifications & Teaching Experience
        $response = $this->postJson(route('public.careers.apply.save-step'), [
            'step' => 6,
            'vacancy_id' => $vacancy->id,
            'application_id' => $appId,
            'teaching_subjects' => ['English', 'Kiswahili'],
            'teaching_other_subjects' => 'Literature in English',
            'teaching_years' => '4',
            'teaching_level' => 'Secondary',
            'teaching_institution' => 'Singida Sec',
            'qualifications' => [
                [
                    'name' => 'Teaching Certificate',
                    'registration_number' => 'TC-889922',
                    'date_issued' => '2019-12-01',
                    'expiry_date' => '',
                    'certificate_path' => 'uploaded'
                ]
            ]
        ]);
        $response->assertOk();

        // Step 7: Motivation Letter
        $response = $this->postJson(route('public.careers.apply.save-step'), [
            'step' => 7,
            'vacancy_id' => $vacancy->id,
            'application_id' => $appId,
            'motivation_letter' => 'I would love to contribute to STTC.'
        ]);
        $response->assertOk();

        // Step 8: Attachments Upload (Mark required docs as uploaded)
        // Passport photo uploaded in Step 2 is already in application attachments
        $application->update([
            'attachments' => [
                'cv' => 'job_documents/cv.pdf',
                'cover_letter' => 'job_documents/cl.pdf',
                'academic_certificates' => 'job_documents/ac.pdf',
                'academic_transcripts' => 'job_documents/at.pdf',
                'nida' => 'job_documents/nida.pdf',
                'passport_photo' => 'job_documents/pp.jpg',
            ]
        ]);

        $response = $this->postJson(route('public.careers.apply.save-step'), [
            'step' => 8,
            'vacancy_id' => $vacancy->id,
            'application_id' => $appId
        ]);
        $response->assertOk();

        // Step 9: Declaration & Digital Signature
        $response = $this->postJson(route('public.careers.apply.save-step'), [
            'step' => 9,
            'vacancy_id' => $vacancy->id,
            'application_id' => $appId,
            'certified_correct' => '1',
            'digital_signature' => 'data:image/png;base64,fake_signature_canvas'
        ]);
        $response->assertOk();

        $freshApp = $application->fresh();
        $this->assertEquals('Submitted', $freshApp->status);
        $this->assertEquals(9, $freshApp->current_step);
        $this->assertNotNull($freshApp->submitted_at);
        $this->assertNotNull($freshApp->declaration_date);
    }

    public function test_admin_can_delete_vacancy()
    {
        $this->actingAs($this->hrManager);

        $category = JobCategory::first() ?? JobCategory::create([
            'name' => 'IT & Engineering',
            'description' => 'Software and infrastructure roles',
            'status' => 'active',
            'display_order' => 1
        ]);

        $designation = Designation::first() ?? Designation::create([
            'name' => 'Information Technology',
            'short_code' => 'IT',
            'head_of_designation_id' => $this->designationHead->id,
            'description' => 'IT desig',
            'status' => 'active'
        ]);

        $position = Position::first() ?? Position::create([
            'name' => 'Senior Laravel Developer',
            'designation_id' => $designation->id,
            'job_category_id' => $category->id,
            'employment_type' => 'Full-time',
            'salary_grade' => 'PG 8',
            'status' => 'active'
        ]);

        $vacancy = Vacancy::create([
            'vacancy_number' => 'VAC-TEST-DELETE',
            'job_title' => 'Test Vacancy to Delete',
            'designation_id' => $designation->id,
            'position_id' => $position->id,
            'job_category_id' => $category->id,
            'number_of_positions' => 1,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida',
            'application_deadline' => now()->addDays(5)->format('Y-m-d'),
            'responsibilities' => 'Responsibilities',
            'qualifications' => 'Qualifications',
            'required_experience' => 'Experience',
            'required_skills' => 'Skills',
            'status' => 'Published'
        ]);

        // Create an application to verify cascade deletion
        $application = JobApplication::create([
            'application_number' => 'APP-2026-DELETE',
            'user_id' => $this->applicant->id,
            'vacancy_id' => $vacancy->id,
            'full_name' => 'John Delete Candidate',
            'email' => 'delete.candidate@example.com',
            'phone' => '+255755111222',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01',
            'current_step' => 1,
            'status' => 'Draft'
        ]);

        $this->assertDatabaseHas('vacancies', ['id' => $vacancy->id]);
        $this->assertDatabaseHas('job_applications', ['id' => $application->id]);

        $response = $this->delete(route('admin.recruitment.vacancies.destroy', $vacancy->id));
        $response->assertRedirect();

        $this->assertDatabaseMissing('vacancies', ['id' => $vacancy->id]);
        $this->assertDatabaseMissing('job_applications', ['id' => $application->id]);
    }

    public function test_admin_can_manage_applicant_login_credentials()
    {
        $this->actingAs($this->hrManager);

        $category = JobCategory::first() ?? JobCategory::create([
            'name' => 'IT & Engineering',
            'description' => 'Software and infrastructure roles',
            'status' => 'active',
            'display_order' => 1
        ]);

        $designation = Designation::first() ?? Designation::create([
            'name' => 'Information Technology',
            'short_code' => 'IT',
            'head_of_designation_id' => $this->designationHead->id,
            'description' => 'IT desig',
            'status' => 'active'
        ]);

        $position = Position::first() ?? Position::create([
            'name' => 'Senior Laravel Developer',
            'designation_id' => $designation->id,
            'job_category_id' => $category->id,
            'employment_type' => 'Full-time',
            'salary_grade' => 'PG 8',
            'status' => 'active'
        ]);

        $vacancy = Vacancy::create([
            'vacancy_number' => 'VAC-TEST-CREDENTIALS',
            'job_title' => 'Test Vacancy Credentials',
            'designation_id' => $designation->id,
            'position_id' => $position->id,
            'job_category_id' => $category->id,
            'number_of_positions' => 1,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida',
            'application_deadline' => now()->addDays(5)->format('Y-m-d'),
            'responsibilities' => 'Responsibilities',
            'qualifications' => 'Qualifications',
            'required_experience' => 'Experience',
            'required_skills' => 'Skills',
            'status' => 'Published'
        ]);

        $application = JobApplication::create([
            'application_number' => 'APP-2026-CRED',
            'user_id' => $this->applicant->id,
            'vacancy_id' => $vacancy->id,
            'full_name' => 'Credentials Test Candidate',
            'email' => 'credentials.candidate@example.com',
            'phone' => '+255755333444',
            'gender' => 'male',
            'date_of_birth' => '1990-01-01',
            'current_step' => 11,
            'status' => 'Applied'
        ]);

        $response = $this->post(route('admin.recruitment.applications.credentials', $application->id), [
            'password_option' => 'custom',
            'custom_password' => 'NewSecurePassword123!',
            'phone' => '+255755333444',
            'whatsapp_number' => '+255755333444',
            'email' => 'credentials.candidate@example.com',
            'channels' => ['sms', 'whatsapp', 'email']
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Test login with the phone number
        \Illuminate\Support\Facades\Auth::logout();

        $loginResponse = $this->postJson('/login', [
            'email' => '+255755333444',
            'password' => 'NewSecurePassword123!',
        ]);

        $loginResponse->assertStatus(200);
        $this->assertAuthenticated();
        
        $user = \Illuminate\Support\Facades\Auth::user();
        $this->assertEquals('+255755333444', $user->phone);
    }
}
