<?php

namespace Database\Factories;

use App\Models\CareerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CareerProfile>
 */
class CareerProfileFactory extends Factory
{
    protected $model = CareerProfile::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'current_profession' => $this->faker->jobTitle(),
            'years_experience' => $this->faker->numberBetween(1, 15),
            'skills' => ['PHP', 'Laravel', 'SQL'],
            'linkedin_url' => 'https://linkedin.com/in/' . $this->faker->userName(),
            'portfolio_url' => $this->faker->url(),
            'cv_path' => 'private/cv/' . $this->faker->uuid() . '.pdf',
            'preferred_job_categories' => ['ICT', 'Engineering'],
            'preferred_locations' => ['Dar es Salaam', 'Dodoma'],
            'expected_salary' => $this->faker->numberBetween(500000, 5000000),
            'availability_date' => $this->faker->dateTimeBetween('now', '+30 days')->format('Y-m-d'),
        ];
    }
}
