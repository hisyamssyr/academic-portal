<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GradeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin/grades/create')->assertRedirect(route('login'));
    }

    public function test_input_nilai_link_is_hidden_for_mahasiswa(): void
    {
        $this->actingAs(User::factory()->mahasiswa()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertDontSee('Input Nilai');
    }

    public function test_input_nilai_link_is_visible_for_staff(): void
    {
        $this->actingAs(User::factory()->dosen()->create())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Input Nilai');
    }

    #[DataProvider('staffRoles')]
    public function test_staff_can_input_grade(UserRole $role): void
    {
        $grader = User::factory()->create(['role' => $role]);
        $student = User::factory()->mahasiswa()->create();

        $response = $this->actingAs($grader)->post('/admin/grades', [
            'student_id' => $student->id,
            'course' => Grade::COURSES[0],
            'score' => 85.5,
            'feedback' => 'Analisis sudah baik.',
        ]);

        $response->assertRedirect(route('admin.grades.index'));
        $this->assertDatabaseHas('grades', [
            'student_id' => $student->id,
            'grader_id' => $grader->id,
            'course' => Grade::COURSES[0],
            'score' => 85.5,
            'feedback' => 'Analisis sudah baik.',
        ]);
    }

    #[DataProvider('roleExpectations')]
    public function test_input_nilai_gate_allows_only_staff(UserRole $role, bool $expected): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->assertSame($expected, Gate::forUser($user)->allows('input-nilai'));
    }

    public function test_mahasiswa_cannot_input_grade(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $otherStudent = User::factory()->mahasiswa()->create();

        $this->actingAs($student)->post('/admin/grades', [
            'student_id' => $otherStudent->id,
            'course' => Grade::COURSES[0],
            'score' => 90,
        ])->assertForbidden();

        $this->assertDatabaseCount('grades', 0);
    }

    public function test_score_above_one_hundred_is_rejected(): void
    {
        $grader = User::factory()->dosen()->create();
        $student = User::factory()->mahasiswa()->create();

        $response = $this->actingAs($grader)->post('/admin/grades', [
            'student_id' => $student->id,
            'course' => Grade::COURSES[0],
            'score' => 120,
        ]);

        $response->assertSessionHasErrors('score');
        $this->assertDatabaseCount('grades', 0);
    }

    public function test_student_id_must_reference_a_mahasiswa(): void
    {
        $grader = User::factory()->dosen()->create();
        $otherDosen = User::factory()->dosen()->create();

        $response = $this->actingAs($grader)->post('/admin/grades', [
            'student_id' => $otherDosen->id,
            'course' => Grade::COURSES[0],
            'score' => 90,
        ]);

        $response->assertSessionHasErrors('student_id');
        $this->assertDatabaseCount('grades', 0);
    }

    public function test_course_must_be_one_of_the_available_courses(): void
    {
        $grader = User::factory()->dosen()->create();
        $student = User::factory()->mahasiswa()->create();

        $response = $this->actingAs($grader)->post('/admin/grades', [
            'student_id' => $student->id,
            'course' => 'Mata Kuliah Tidak Dikenal',
            'score' => 90,
        ]);

        $response->assertSessionHasErrors('course');
        $this->assertDatabaseCount('grades', 0);
    }

    public function test_grade_creation_requires_student_course_and_score(): void
    {
        $grader = User::factory()->dosen()->create();

        $response = $this->actingAs($grader)->post('/admin/grades', []);

        $response->assertSessionHasErrors(['student_id', 'course', 'score']);
        $this->assertDatabaseCount('grades', 0);
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
