<?php

namespace Tests\Feature;

use App\Enums\ProposalStatus;
use App\Enums\UserRole;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_demo_accounts_for_every_role(): void
    {
        $this->seed();

        $this->assertSame(UserRole::Mahasiswa, User::where('email', 'mahasiswa@example.com')->firstOrFail()->role);
        $this->assertSame(UserRole::Mahasiswa, User::where('email', 'mahasiswa2@example.com')->firstOrFail()->role);
        $this->assertSame(UserRole::Asdos, User::where('email', 'asdos@example.com')->firstOrFail()->role);
        $this->assertSame(UserRole::Dosen, User::where('email', 'dosen@example.com')->firstOrFail()->role);
    }

    public function test_seeder_can_be_run_repeatedly_without_duplicates(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('users', 4);
        $this->assertDatabaseCount('project_proposals', 5);
    }

    public function test_seeder_reviews_proposals_with_score_feedback_and_grader(): void
    {
        $this->seed();

        $reviewedProposals = ProjectProposal::where('status', ProposalStatus::Reviewed)->get();

        $this->assertCount(2, $reviewedProposals);

        foreach ($reviewedProposals as $proposal) {
            $this->assertNotNull($proposal->score);
            $this->assertNotNull($proposal->feedback);
            $this->assertNotNull($proposal->grader_id);
            $this->assertNotNull($proposal->reviewed_at);
        }
    }
}
