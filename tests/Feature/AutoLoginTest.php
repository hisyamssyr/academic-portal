<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AutoLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_auto_login_logs_in_mahasiswa_on_local_environment(): void
    {
        $this->app['env'] = 'local';

        $student = User::factory()->mahasiswa()->create();

        $response = $this->get('/login/as/mahasiswa');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($student);
    }

    #[DataProvider('staffRoles')]
    public function test_auto_login_logs_in_staff_on_local_environment(UserRole $role): void
    {
        $this->app['env'] = 'local';

        $user = User::factory()->create(['role' => $role]);

        $response = $this->get('/login/as/'.$role->value);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_auto_login_is_not_available_outside_local_environment(): void
    {
        User::factory()->dosen()->create();

        $this->get('/login/as/dosen')->assertNotFound();
        $this->assertGuest();
    }

    public function test_auto_login_returns_404_when_no_demo_user_exists(): void
    {
        $this->app['env'] = 'local';

        $this->get('/login/as/dosen')->assertNotFound();
        $this->assertGuest();
    }

    public function test_auto_login_rejects_unknown_role(): void
    {
        $this->app['env'] = 'local';

        $this->get('/login/as/admin')->assertNotFound();
        $this->assertGuest();
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
