<?php

namespace Database\Factories;

use App\Models\Grade;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_id' => User::factory()->mahasiswa(),
            'grader_id' => User::factory()->dosen(),
            'course' => fake()->randomElement(Grade::COURSES),
            'score' => fake()->randomFloat(2, 0, 100),
            'feedback' => fake()->optional()->sentence(),
        ];
    }
}
