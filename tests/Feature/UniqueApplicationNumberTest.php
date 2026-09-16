<?php

namespace Tests\Feature;

use App\Models\AcademicYear;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\Intake;
use App\Models\JobApplication;
use App\Models\Programme;
use App\Models\User;
use App\Services\ApplicationWorkflowService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UniqueApplicationNumberTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_generates_unique_application_numbers_sequentially(): void
    {
        $year = (int) date('Y');

        $num1 = ApplicationWorkflowService::generateUniqueApplicationNumber($year);
        $this->assertMatchesRegularExpression('/^SUPA-' . $year . '-\d{6}$/', $num1);

        $user1 = User::factory()->create();
        $applicant1 = Applicant::create(['user_id' => $user1->id, 'gender' => 'male', 'date_of_birth' => '2000-01-01']);
        $prog = Programme::first() ?? Programme::create(['name' => 'IT', 'code' => 'IT01', 'department' => 'IT', 'duration_years' => 3, 'application_fee' => 20000]);
        $ay = AcademicYear::first() ?? AcademicYear::create(['name' => '2026/2027', 'is_active' => true, 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
        $intake = Intake::first() ?? Intake::create(['name' => 'September', 'academic_year_id' => $ay->id, 'start_date' => '2026-09-01', 'end_date' => '2026-09-30', 'is_active' => true]);

        $app1 = Application::create([
            'applicant_id' => $applicant1->id,
            'application_number' => $num1,
            'programme_id' => $prog->id,
            'academic_year_id' => $ay->id,
            'intake_id' => $intake->id,
            'admission_type' => 'Direct Entry',
            'admission_category' => 'Direct Entry',
            'status' => 'Draft',
        ]);

        $num2 = ApplicationWorkflowService::generateUniqueApplicationNumber($year);
        $this->assertNotEquals($num1, $num2);

        $user2 = User::factory()->create();
        $applicant2 = Applicant::create(['user_id' => $user2->id, 'gender' => 'female', 'date_of_birth' => '2001-02-02']);

        $app2 = Application::create([
            'applicant_id' => $applicant2->id,
            'application_number' => $num2,
            'programme_id' => $prog->id,
            'academic_year_id' => $ay->id,
            'intake_id' => $intake->id,
            'admission_type' => 'Direct Entry',
            'admission_category' => 'Direct Entry',
            'status' => 'Draft',
        ]);

        $this->assertNotEquals($app1->application_number, $app2->application_number);
    }

    public function test_generates_unique_job_application_numbers(): void
    {
        $year = (int) date('Y');
        $jobNum1 = JobApplication::generateUniqueApplicationNumber($year);
        $this->assertMatchesRegularExpression('/^SUPA-JOB-' . $year . '-\d{6}$/', $jobNum1);

        $user = User::factory()->create();
        JobApplication::create([
            'application_number' => $jobNum1,
            'user_id' => $user->id,
            'vacancy_id' => 1,
            'status' => 'Draft',
            'current_step' => 1,
            'full_name' => 'John Doe',
            'gender' => 'male',
            'date_of_birth' => '1995-01-01',
            'phone' => '0712345678',
            'email' => 'john@example.com',
        ]);

        $jobNum2 = JobApplication::generateUniqueApplicationNumber($year);
        $this->assertNotEquals($jobNum1, $jobNum2);
    }

    public function test_database_enforces_unique_application_number_constraint(): void
    {
        $user1 = User::factory()->create();
        $applicant1 = Applicant::create(['user_id' => $user1->id, 'gender' => 'male', 'date_of_birth' => '2000-01-01']);
        $prog = Programme::first() ?? Programme::create(['name' => 'IT', 'code' => 'IT01', 'department' => 'IT', 'duration_years' => 3, 'application_fee' => 20000]);
        $ay = AcademicYear::first() ?? AcademicYear::create(['name' => '2026/2027', 'is_active' => true, 'start_date' => '2026-01-01', 'end_date' => '2026-12-31']);
        $intake = Intake::first() ?? Intake::create(['name' => 'September', 'academic_year_id' => $ay->id, 'start_date' => '2026-09-01', 'end_date' => '2026-09-30', 'is_active' => true]);

        Application::create([
            'applicant_id' => $applicant1->id,
            'application_number' => 'SUPA-2026-UNIQUE-TEST',
            'programme_id' => $prog->id,
            'academic_year_id' => $ay->id,
            'intake_id' => $intake->id,
            'admission_type' => 'Direct Entry',
            'admission_category' => 'Direct Entry',
            'status' => 'Draft',
        ]);

        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);

        $user2 = User::factory()->create();
        $applicant2 = Applicant::create(['user_id' => $user2->id, 'gender' => 'female', 'date_of_birth' => '2001-02-02']);

        Application::create([
            'applicant_id' => $applicant2->id,
            'application_number' => 'SUPA-2026-UNIQUE-TEST',
            'programme_id' => $prog->id,
            'academic_year_id' => $ay->id,
            'intake_id' => $intake->id,
            'admission_type' => 'Direct Entry',
            'admission_category' => 'Direct Entry',
            'status' => 'Draft',
        ]);
    }
}
