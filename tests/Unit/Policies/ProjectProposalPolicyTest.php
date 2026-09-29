<?php

namespace Tests\Unit\Policies;

use App\Enums\UserRole;
use App\Models\ProjectProposal;
use App\Models\User;
use App\Policies\ProjectProposalPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProjectProposalPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_owner_can_update_proposal(): void
    {
        $owner = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($owner)->create();

        $this->assertTrue((new ProjectProposalPolicy)->update($owner, $proposal));
    }

    public function test_other_student_cannot_update_proposal(): void
    {
        $owner = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($owner)->create();
        $otherStudent = User::factory()->mahasiswa()->create();

        $this->assertFalse((new ProjectProposalPolicy)->update($otherStudent, $proposal));
    }

    #[DataProvider('staffRoles')]
    public function test_staff_cannot_update_proposal(UserRole $role): void
    {
        $owner = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($owner)->create();
        $staff = User::factory()->create(['role' => $role]);

        $this->assertFalse((new ProjectProposalPolicy)->update($staff, $proposal));
    }

    public function test_owner_can_submit_draft_proposal(): void
    {
        $owner = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($owner)->create();

        $this->assertTrue((new ProjectProposalPolicy)->submit($owner, $proposal));
    }

    public function test_owner_cannot_submit_submitted_proposal(): void
    {
        $owner = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($owner)->submitted()->create();

        $this->assertFalse((new ProjectProposalPolicy)->submit($owner, $proposal));
    }

    public function test_owner_cannot_submit_reviewed_proposal(): void
    {
        $owner = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($owner)->reviewed()->create();

        $this->assertFalse((new ProjectProposalPolicy)->submit($owner, $proposal));
    }

    public function test_other_student_cannot_submit_draft_proposal(): void
    {
        $owner = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($owner)->create();
        $otherStudent = User::factory()->mahasiswa()->create();

        $this->assertFalse((new ProjectProposalPolicy)->submit($otherStudent, $proposal));
    }

    /**
     * @return array<string, array<int, UserRole>>
     */
    public static function staffRoles(): array
    {
        return [
            'asdos' => [UserRole::Asdos],
            'dosen' => [UserRole::Dosen],
        ];
    }
}
