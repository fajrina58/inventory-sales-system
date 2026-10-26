<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_dashboard_and_is_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_login_with_invalid_password_is_rejected(): void
    {
        $user = User::factory()->create([
            'email' => 'user@inventory.test',
            'password' => bcrypt('correct-password'),
            'role' => 'staff',
        ]);

        $response = $this->post('/login', [
            'email' => 'user@inventory.test',
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'role' => 'staff',
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
    }

    public function test_staff_cannot_access_admin_user_management_and_receives_403_forbidden(): void
    {
        $staff = User::factory()->create([
            'role' => 'staff',
        ]);

        // Mencoba akses langsung via URL
        $response = $this->actingAs($staff)->get('/admin/users');

        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_user_management(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertSee('Kelola Pengguna');
    }
}
