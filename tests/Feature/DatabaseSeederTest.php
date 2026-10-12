<?php

namespace Tests\Feature;

use App\Enums\ProposalStatus;
use App\Enums\UserRole;
use App\Models\Project;
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

    public function test_seeder_creates_fifteen_students_with_two_projects_each(): void
    {
        $this->seed();

        $students = User::where('email', 'like', 'mahasiswa%@student.its.ac.id')->get();

        $this->assertCount(15, $students);

        foreach ($students as $student) {
            $this->assertSame(UserRole::Mahasiswa, $student->role);
            $this->assertSame(2, $student->projects()->count());
        }

        $this->assertSame(30, Project::whereIn('user_id', $students->pluck('id'))->count());
    }

    public function test_seeder_creates_examiner_demo_account_with_ready_portfolio(): void
    {
        $this->seed();

        $examiner = User::where('email', 'dosenpbkk@its.ac.id')->firstOrFail();

        $this->assertSame(UserRole::Dosen, $examiner->role);
        $this->assertTrue($examiner->projects()->exists());
    }

    public function test_seeder_can_be_run_repeatedly_without_duplicates(): void
    {
        $this->seed();
        $this->seed();

        $this->assertDatabaseCount('users', 20);
        $this->assertDatabaseCount('projects', 33);
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
