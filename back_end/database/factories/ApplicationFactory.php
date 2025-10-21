<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Application>
 */
class ApplicationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::inRandomOrder()->first()->id,
            'job_id' => \App\Models\Job::inRandomOrder()->first()->id,
            'phone_number' => $this->faker->phoneNumber(),
            'cover_letter' => $this->faker->paragraph(2),
            'resume_path' => 'resumes/' . $this->faker->uuid() . '.pdf',
        ];
    }
}
