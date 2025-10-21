<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Job;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Job>
 */
class JobFactory extends Factory
{
    protected $model = Job::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $employmentTypes = ['full-time', 'part-time', 'contract', 'internship'];

        return [
            'title' => $this->faker->jobTitle(),
            'description' => $this->faker->paragraph(3),
            'company_name' => $this->faker->company(),
            'location' => $this->faker->city(),
            'employment_type' => $this->faker->randomElement($employmentTypes),
            'salary' => $this->faker->numberBetween(20000, 100000),
            'posted_by' => User::inRandomOrder()->first()->id, 
        ];
    }
}
