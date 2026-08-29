<?php

namespace Database\Factories;

use App\Models\ExternalApplicationRedirect;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ExternalApplicationRedirect>
 */
class ExternalApplicationRedirectFactory extends Factory
{
    protected $model = ExternalApplicationRedirect::class;

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
                    'vacancy_number' => 'VAC-' . date('Y') . '-' . strtoupper(Str::random(6)),
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
            'provider' => 'ajiramarket',
            'tracking_token' => hash('sha256', Str::uuid()),
            'destination_url' => $this->faker->url(),
            'ip_address' => $this->faker->ipv4(),
            'user_agent' => $this->faker->userAgent(),
            'redirected_at' => now(),
        ];
    }
}
