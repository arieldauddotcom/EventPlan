<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_user_management(): void
    {
        $regularUser = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($regularUser)->get(route('admin.users.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_view_user_management_index_and_see_menu(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $otherUser = User::factory()->create([
            'name' => 'Target Pengguna',
            'email' => 'target@example.com',
            'role' => 'user',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.users.index'));

        $response->assertStatus(200);
        $response->assertSee('Kelola Pengguna');
        $response->assertSee('Target Pengguna');
        $response->assertSee('target@example.com');
    }

    public function test_admin_can_update_user_information(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $targetUser = User::factory()->create([
            'name' => 'Old Name',
            'username' => 'olduser',
            'email' => 'old@example.com',
            'role' => 'user',
        ]);

        $response = $this->actingAs($admin)->put(route('admin.users.update', $targetUser), [
            'name' => 'New Admin Edited Name',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'role' => 'admin',
            'new_password' => 'secret12345',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $targetUser->refresh();
        $this->assertEquals('New Admin Edited Name', $targetUser->name);
        $this->assertEquals('admin', $targetUser->role);
        $this->assertTrue(Hash::check('secret12345', $targetUser->password));
    }

    public function test_admin_can_delete_other_user(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $targetUser = User::factory()->create([
            'role' => 'user',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $targetUser));

        $response->assertRedirect(route('admin.users.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_admin_cannot_delete_self(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->delete(route('admin.users.destroy', $admin));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }
}
