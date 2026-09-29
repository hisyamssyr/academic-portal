<?php

namespace Database\Factories;

use App\Enums\ProposalStatus;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProjectProposal>
 */
class ProjectProposalFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->mahasiswa(),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'status' => ProposalStatus::Draft,
        ];
    }

    /**
     * Indicate that the proposal has been submitted for review.
     */
    public function submitted(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProposalStatus::Submitted,
        ]);
    }

    /**
     * Indicate that the proposal has been reviewed and graded.
     */
    public function reviewed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ProposalStatus::Reviewed,
            'score' => fake()->randomFloat(2, 70, 100),
            'feedback' => fake()->sentence(),
            'grader_id' => User::factory()->dosen(),
            'reviewed_at' => now(),
        ]);
    }
}
