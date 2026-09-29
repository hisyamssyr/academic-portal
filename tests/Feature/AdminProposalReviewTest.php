<?php

namespace Tests\Feature;

use App\Enums\ProposalStatus;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminProposalReviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_staff_can_filter_proposals_by_status(): void
    {
        $draft = ProjectProposal::factory()->create(['title' => 'Proposal Draft']);
        $submitted = ProjectProposal::factory()->submitted()->create(['title' => 'Proposal Diajukan']);

        $this->actingAs(User::factory()->dosen()->create())
            ->get('/admin/proposals?status=submitted')
            ->assertOk()
            ->assertSee($submitted->title)
            ->assertDontSee($draft->title);
    }

    public function test_staff_can_update_proposal_status(): void
    {
        $proposal = ProjectProposal::factory()->submitted()->create();

        $response = $this->actingAs(User::factory()->dosen()->create())
            ->patch(route('admin.proposals.update', $proposal), [
                'status' => ProposalStatus::Reviewed->value,
            ]);

        $response->assertRedirect(route('admin.proposals.show', $proposal));
        $this->assertSame(ProposalStatus::Reviewed, $proposal->refresh()->status);
    }

    public function test_proposal_status_must_be_a_known_status(): void
    {
        $proposal = ProjectProposal::factory()->submitted()->create();

        $response = $this->actingAs(User::factory()->asdos()->create())
            ->patch(route('admin.proposals.update', $proposal), [
                'status' => 'unknown-status',
            ]);

        $response->assertSessionHasErrors('status');
        $this->assertSame(ProposalStatus::Submitted, $proposal->refresh()->status);
    }

    public function test_staff_can_view_proposal_detail_with_student_information(): void
    {
        $student = User::factory()->mahasiswa()->create(['name' => 'Budi Mahasiswa']);
        $proposal = ProjectProposal::factory()->for($student)->create(['title' => 'Proposal Budi']);

        $this->actingAs(User::factory()->asdos()->create())
            ->get(route('admin.proposals.show', $proposal))
            ->assertOk()
            ->assertSee('Proposal Budi')
            ->assertSee('Budi Mahasiswa')
            ->assertSee($student->email);
    }
}
