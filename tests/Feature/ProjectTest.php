<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_factory_creates_project_owned_by_a_student(): void
    {
        $project = Project::factory()->create();

        $this->assertInstanceOf(User::class, $project->user);
        $this->assertSame(UserRole::Mahasiswa, $project->user->role);
    }

    public function test_project_is_mass_assignable_through_fillable_attributes(): void
    {
        $user = User::factory()->create();

        $project = Project::factory()->for($user)->create([
            'judul' => 'Agen Otonom Perencana Jadwal Kuliah',
            'tema_agent' => 'LangChain',
        ]);

        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'user_id' => $user->id,
            'judul' => 'Agen Otonom Perencana Jadwal Kuliah',
            'tema_agent' => 'LangChain',
        ]);
    }

    public function test_api_key_is_encrypted_at_rest_and_hidden_from_serialization(): void
    {
        $project = Project::factory()->create();

        $stored = DB::table('projects')->where('id', $project->id)->value('api_key_secure');

        $this->assertNotSame($project->api_key_secure, $stored);
        $this->assertArrayNotHasKey('api_key_secure', $project->toArray());
    }

    public function test_deskripsi_can_be_null(): void
    {
        $project = Project::factory()->tanpaDeskripsi()->create();

        $this->assertNull($project->deskripsi);
    }

    public function test_tema_agent_falls_back_to_ollama_when_omitted(): void
    {
        $project = Project::query()->create([
            'user_id' => User::factory()->create()->id,
            'judul' => 'Agen Tanpa Tema',
            'api_key_secure' => 'sk-'.str_repeat('a', 48),
        ]);

        $this->assertSame('Ollama', $project->refresh()->tema_agent);
    }
}
