<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Role;
use App\Models\Designation;
use App\Models\Position;
use App\Models\JobCategory;
use App\Models\Vacancy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VacancyExternalRulesTest extends TestCase
{
    use RefreshDatabase;

    protected $hrManager;
    protected $designation;
    protected $position;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $managerRole = Role::where('name', 'hr_manager')->first();
        $this->hrManager = User::factory()->create(['role' => 'registrar']);
        $this->hrManager->roles()->attach($managerRole);

        $this->category = JobCategory::first();
        $this->designation = Designation::first();
        $this->position = Position::first();
    }

    public function test_external_vacancy_requires_external_url()
    {
        $this->actingAs($this->hrManager);

        $data = [
            'job_title' => 'Software Instructor',
            'department_name' => 'IT Department',
            'designation_id' => $this->designation->id,
            'position_id' => $this->position->id,
            'job_category_id' => $this->category->id,
            'number_of_positions' => 1,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida',
            'recommended_region' => 'Singida',
            'application_deadline' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'Published',
            'responsibilities' => 'Teaching software development',
            'qualifications' => 'BSc CS',
            'required_experience' => '3 years',
            'required_skills' => 'Laravel, PHP',
            'application_type' => 'external',
            // external_url is missing
        ];

        $response = $this->post(route('admin.recruitment.vacancies.store'), $data);
        $response->assertSessionHasErrors(['external_url']);
    }

    public function test_internal_vacancy_uses_ats_flow()
    {
        $vacancy = Vacancy::create([
            'vacancy_number' => 'VAC-INT-123',
            'job_title' => 'Internal Teacher',
            'designation_id' => $this->designation->id,
            'position_id' => $this->position->id,
            'job_category_id' => $this->category->id,
            'number_of_positions' => 1,
            'employment_type' => 'Full-time',
            'contract_type' => 'Permanent',
            'location' => 'Singida',
            'application_deadline' => now()->addDays(10)->format('Y-m-d'),
            'status' => 'Published',
            'responsibilities' => 'Teaching',
            'qualifications' => 'BSc',
            'required_experience' => '3 years',
            'required_skills' => 'Teaching',
            'application_type' => 'internal',
        ]);

        $this->assertFalse($vacancy->isExternal());
    }
}
