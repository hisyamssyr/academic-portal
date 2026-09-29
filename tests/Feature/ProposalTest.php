<?php

namespace Tests\Feature;

use App\Enums\ProposalStatus;
use App\Enums\UserRole;
use App\Models\ProjectProposal;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ProposalTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/proposals')->assertRedirect(route('login'));
    }

    public function test_student_can_create_proposal_as_draft(): void
    {
        $student = User::factory()->mahasiswa()->create();

        $response = $this->actingAs($student)->post('/proposals', [
            'title' => 'Deteksi Objek dengan YOLO',
            'description' => 'Rencana proyek AI untuk deteksi objek secara real time.',
        ]);

        $response->assertRedirect(route('proposals.index'));
        $this->assertDatabaseHas('project_proposals', [
            'user_id' => $student->id,
            'title' => 'Deteksi Objek dengan YOLO',
            'status' => ProposalStatus::Draft->value,
        ]);
    }

    public function test_proposal_creation_requires_title_and_description(): void
    {
        $student = User::factory()->mahasiswa()->create();

        $response = $this->actingAs($student)->post('/proposals', [
            'title' => '',
            'description' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'description']);
        $this->assertDatabaseCount('project_proposals', 0);
    }

    public function test_student_can_edit_own_proposal(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($student)->create();

        $this->actingAs($student)
            ->get(route('proposals.edit', $proposal))
            ->assertOk();

        $response = $this->actingAs($student)->patch(route('proposals.update', $proposal), [
            'title' => 'Judul Baru',
            'description' => 'Deskripsi baru.',
        ]);

        $response->assertRedirect(route('proposals.index'));
        $this->assertDatabaseHas('project_proposals', [
            'id' => $proposal->id,
            'title' => 'Judul Baru',
        ]);
    }

    public function test_student_cannot_edit_another_students_proposal(): void
    {
        $owner = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($owner)->create(['title' => 'Proposal Pemilik']);
        $otherStudent = User::factory()->mahasiswa()->create();

        $this->actingAs($otherStudent)
            ->get(route('proposals.edit', $proposal))
            ->assertForbidden();

        $this->actingAs($otherStudent)->patch(route('proposals.update', $proposal), [
            'title' => 'Dibajak',
            'description' => 'Percobaan edit.',
        ])->assertForbidden();

        $this->assertDatabaseHas('project_proposals', [
            'id' => $proposal->id,
            'title' => 'Proposal Pemilik',
        ]);
    }

    public function test_student_can_submit_own_draft_proposal(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($student)->create();

        $response = $this->actingAs($student)->patch(route('proposals.submit', $proposal));

        $response->assertRedirect(route('proposals.index'));
        $this->assertSame(ProposalStatus::Submitted, $proposal->refresh()->status);
    }

    public function test_student_cannot_submit_a_proposal_that_is_not_a_draft(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $proposal = ProjectProposal::factory()->for($student)->submitted()->create();

        $this->actingAs($student)
            ->patch(route('proposals.submit', $proposal))
            ->assertForbidden();

        $this->assertSame(ProposalStatus::Submitted, $proposal->refresh()->status);
    }

    #[DataProvider('staffRoles')]
    public function test_staff_cannot_access_student_proposal_area(UserRole $role): void
    {
        $this->actingAs(User::factory()->create(['role' => $role]))
            ->get('/proposals')
            ->assertForbidden();
    }

    public function test_fourth_proposal_submission_within_a_minute_is_throttled(): void
    {
        $student = User::factory()->mahasiswa()->create();

        foreach (range(1, 3) as $attempt) {
            $this->actingAs($student)->post('/proposals', [
                'title' => "Proposal {$attempt}",
                'description' => "Deskripsi proposal {$attempt}.",
            ])->assertRedirect(route('proposals.index'));
        }

        $this->actingAs($student)->post('/proposals', [
            'title' => 'Proposal keempat',
            'description' => 'Deskripsi proposal keempat.',
        ])->assertTooManyRequests();

        $this->assertDatabaseCount('project_proposals', 3);
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
