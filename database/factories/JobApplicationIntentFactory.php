<?php

namespace Database\Factories;

use App\Models\JobApplicationIntent;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobApplicationIntent>
 */
class JobApplicationIntentFactory extends Factory
{
    protected $model = JobApplicationIntent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'vacancy_id' => function() {
                return Vacancy::first()?->id ?? Vacancy::create([
                    'vacancy_number' => 'VAC-' . date('Y') . '-' . strtoupper(\Illuminate\Support\Str::random(6)),
                    'job_title' => $this->faker->jobTitle(),
                    'designation_id' => \App\Models\Designation::first()?->id ?? 1,
                    'position_id' => \App\Models\Position::first()?->id ?? 1,
                    'job_category_id' => \App\Models\JobCategory::first()?->id ?? 1,
                    'number_of_positions' => 1,
                    'employment_type' => 'Full-time',
                    'contract_type' => 'Permanent',
                    'location' => 'Dar es Salaam',
                    'application_deadline' => now()->addDays(30)->format('Y-m-d'),
                    'status' => 'Published',
                    'responsibilities' => 'Responsibilities',
                    'qualifications' => 'Qualifications',
                    'required_experience' => '3 years',
                    'required_skills' => 'PHP',
                    'application_type' => 'external',
                    'external_url' => 'https://ajiramarket.co.tz/jobs/test-job',
                ])->id;
            },
            'status' => 'started',
            'source' => 'supa-careers',
            'notes' => $this->faker->sentence(),
        ];
    }
}
