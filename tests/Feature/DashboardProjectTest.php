<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_lists_only_the_authenticated_users_projects_newest_first(): void
    {
        $student = User::factory()->mahasiswa()->create();
        $other = User::factory()->mahasiswa()->create();

        $older = Project::factory()->for($student)->create(['judul' => 'Project Lama']);
        $older->forceFill(['created_at' => now()->subDay()])->save();

        Project::factory()->for($student)->create(['judul' => 'Project Baru']);

        Project::factory()->for($other)->create(['judul' => 'Project Milik Orang Lain']);

        $response = $this->actingAs($student)->get('/dashboard');

        $response->assertOk();
        $response->assertSeeInOrder(['Project Baru', 'Project Lama']);
        $response->assertDontSee('Project Milik Orang Lain');
    }

    public function test_dashboard_requires_authentication(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }
}
