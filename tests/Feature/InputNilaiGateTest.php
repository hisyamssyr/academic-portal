<?php

namespace Tests\Feature;

use App\Enums\ProposalStatus;
use App\Enums\UserRole;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class InputNilaiGateTest extends TestCase
{
    use RefreshDatabase;

    #[DataProvider('roleExpectations')]
    public function test_input_nilai_gate_allows_only_staff(UserRole $role, bool $expected): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->assertSame($expected, Gate::forUser($user)->allows('input-nilai'));
    }

    public function test_review_link_is_hidden_for_mahasiswa_on_dashboard(): void
    {
        $this->actingAs(User::factory()->mahasiswa()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Review Proposal');
    }

    public function test_review_link_is_visible_for_staff_on_dashboard(): void
    {
        $this->actingAs(User::factory()->dosen()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Review Proposal');
    }

    public function test_mahasiswa_cannot_review_proposal_by_direct_url(): void
    {
        $proposal = ProjectProposal::factory()->submitted()->create();

        $this->actingAs(User::factory()->mahasiswa()->create())
            ->patch(route('admin.proposals.review', $proposal), [
                'score' => 100,
                'feedback' => 'Percobaan akses langsung.',
            ])
            ->assertForbidden();

        $this->assertSame(ProposalStatus::Submitted, $proposal->refresh()->status);
    }

    /**
     * @return array<string, array{0: UserRole, 1: bool}>
     */
    public static function roleExpectations(): array
    {
        return [
            'mahasiswa' => [UserRole::Mahasiswa, false],
            'asdos' => [UserRole::Asdos, true],
            'dosen' => [UserRole::Dosen, true],
        ];
    }
}
