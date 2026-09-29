<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/admin')->assertRedirect(route('login'));
    }

    public function test_mahasiswa_is_forbidden(): void
    {
        $this->actingAs(User::factory()->mahasiswa()->create())
            ->get('/admin')
            ->assertForbidden();
    }

    #[DataProvider('staffRoles')]
    public function test_staff_can_access_admin_dashboard(UserRole $role): void
    {
        $this->actingAs(User::factory()->create(['role' => $role]))
            ->get('/admin')
            ->assertOk()
            ->assertSee('Panel Admin');
    }

    #[DataProvider('staffRoles')]
    public function test_staff_can_access_admin_proposals(UserRole $role): void
    {
        $this->actingAs(User::factory()->create(['role' => $role]))
            ->get('/admin/proposals')
            ->assertOk()
            ->assertSee('Review Proposal');
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
