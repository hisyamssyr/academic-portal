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

    public function test_review_list_shows_submitted_revised_and_reviewed_proposals_but_not_drafts(): void
    {
        $submitted = ProjectProposal::factory()->submitted()->create(['title' => 'Proposal Diajukan']);
        $revised = ProjectProposal::factory()->reviewed()->create([
            'title' => 'Proposal Revisi',
            'status' => ProposalStatus::Revised,
        ]);
        $reviewed = ProjectProposal::factory()->reviewed()->create(['title' => 'Proposal Direview']);
        $draft = ProjectProposal::factory()->create(['title' => 'Proposal Draft']);

        $this->actingAs(User::factory()->dosen()->create())
            ->get('/admin/proposals')
            ->assertOk()
            ->assertSee($submitted->title)
            ->assertSee($revised->title)
            ->assertSee($reviewed->title)
            ->assertDontSee($draft->title);
    }

    public function test_staff_can_filter_proposals_by_status(): void
    {
        $submitted = ProjectProposal::factory()->submitted()->create(['title' => 'Proposal Diajukan']);
        $revised = ProjectProposal::factory()->reviewed()->create([
            'title' => 'Proposal Revisi',
            'status' => ProposalStatus::Revised,
        ]);
        $reviewed = ProjectProposal::factory()->reviewed()->create(['title' => 'Proposal Direview']);

        $this->actingAs(User::factory()->asdos()->create())
            ->get('/admin/proposals?status=submitted')
            ->assertOk()
            ->assertSee($submitted->title)
            ->assertDontSee($revised->title)
            ->assertDontSee($reviewed->title);
    }

    public function test_staff_can_view_proposal_detail_with_student_information(): void
    {
        $student = User::factory()->mahasiswa()->create(['name' => 'Budi Mahasiswa']);
        $proposal = ProjectProposal::factory()->for($student)->submitted()->create(['title' => 'Proposal Budi']);

        $this->actingAs(User::factory()->asdos()->create())
            ->get(route('admin.proposals.show', $proposal))
            ->assertOk()
            ->assertSee('Proposal Budi')
            ->assertSee('Budi Mahasiswa')
            ->assertSee($student->email);
    }

    public function test_staff_cannot_view_draft_proposal_detail(): void
    {
        $proposal = ProjectProposal::factory()->create();

        $this->actingAs(User::factory()->dosen()->create())
            ->get(route('admin.proposals.show', $proposal))
            ->assertNotFound();
    }

    public function test_staff_can_review_submitted_proposal_with_score_and_feedback(): void
    {
        $proposal = ProjectProposal::factory()->submitted()->create();
        $grader = User::factory()->dosen()->create();

        $response = $this->actingAs($grader)->patch(route('admin.proposals.review', $proposal), [
            'score' => 88.5,
            'feedback' => 'Analisis kuat, perkuat validasi dataset.',
        ]);

        $response->assertRedirect(route('admin.proposals.show', $proposal));
        $this->assertDatabaseHas('project_proposals', [
            'id' => $proposal->id,
            'status' => ProposalStatus::Reviewed->value,
            'score' => 88.5,
            'feedback' => 'Analisis kuat, perkuat validasi dataset.',
            'grader_id' => $grader->id,
        ]);
        $this->assertNotNull($proposal->refresh()->reviewed_at);
    }

    public function test_staff_can_correct_a_reviewed_proposal(): void
    {
        $proposal = ProjectProposal::factory()->reviewed()->create();

        $this->actingAs(User::factory()->asdos()->create())
            ->patch(route('admin.proposals.review', $proposal), [
                'score' => 95,
                'feedback' => 'Revisi sudah bagus.',
            ])
            ->assertRedirect(route('admin.proposals.show', $proposal));

        $this->assertDatabaseHas('project_proposals', [
            'id' => $proposal->id,
            'score' => 95,
            'feedback' => 'Revisi sudah bagus.',
            'status' => ProposalStatus::Reviewed->value,
        ]);
    }

    public function test_staff_can_review_a_revised_proposal(): void
    {
        $proposal = ProjectProposal::factory()->reviewed()->create([
            'status' => ProposalStatus::Revised,
            'score' => 70,
        ]);
        $grader = User::factory()->dosen()->create();

        $this->actingAs($grader)
            ->patch(route('admin.proposals.review', $proposal), [
                'score' => 92,
                'feedback' => 'Revisi diterima.',
            ])
            ->assertRedirect(route('admin.proposals.show', $proposal));

        $this->assertDatabaseHas('project_proposals', [
            'id' => $proposal->id,
            'status' => ProposalStatus::Reviewed->value,
            'score' => 92,
            'feedback' => 'Revisi diterima.',
            'grader_id' => $grader->id,
        ]);
    }

    public function test_review_requires_a_score(): void
    {
        $proposal = ProjectProposal::factory()->submitted()->create();

        $response = $this->actingAs(User::factory()->dosen()->create())
            ->patch(route('admin.proposals.review', $proposal), [
                'score' => '',
                'feedback' => 'Tanpa nilai.',
            ]);

        $response->assertSessionHasErrors('score');
        $this->assertSame(ProposalStatus::Submitted, $proposal->refresh()->status);
    }

    public function test_score_above_one_hundred_is_rejected(): void
    {
        $proposal = ProjectProposal::factory()->submitted()->create();

        $response = $this->actingAs(User::factory()->dosen()->create())
            ->patch(route('admin.proposals.review', $proposal), [
                'score' => 120,
            ]);

        $response->assertSessionHasErrors('score');
        $this->assertSame(ProposalStatus::Submitted, $proposal->refresh()->status);
    }

    public function test_staff_cannot_review_a_draft_proposal(): void
    {
        $proposal = ProjectProposal::factory()->create();

        $this->actingAs(User::factory()->dosen()->create())
            ->patch(route('admin.proposals.review', $proposal), [
                'score' => 80,
            ])
            ->assertNotFound();

        $this->assertSame(ProposalStatus::Draft, $proposal->refresh()->status);
    }
}
