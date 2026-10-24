<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile_page(): void
    {
        $response = $this->get(route('profile.edit'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_profile_edit_page(): void
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email' => 'john@example.com',
            'role' => 'user',
        ]);

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee('Profil Saya');
        $response->assertSee('johndoe');
        $response->assertSee('john@example.com');
    }

    public function test_user_can_update_name_username_email_and_avatar(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'username' => 'olduser',
            'email' => 'old@example.com',
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'New Name',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'avatar' => 'https://example.com/avatar.png',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'New Name',
            'username' => 'newuser',
            'email' => 'new@example.com',
            'avatar' => 'https://example.com/avatar.png',
        ]);
    }

    public function test_profile_update_validates_unique_username_and_email(): void
    {
        $otherUser = User::factory()->create([
            'username' => 'takenuser',
            'email' => 'taken@example.com',
        ]);

        $user = User::factory()->create([
            'username' => 'myusername',
            'email' => 'my@example.com',
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => 'Updated Name',
            'username' => 'takenuser',
            'email' => 'taken@example.com',
        ]);

        $response->assertSessionHasErrors(['username', 'email']);
    }

    public function test_user_can_update_password_with_valid_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-secret123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'username' => $user->username ?? 'validuser',
            'email' => $user->email,
            'current_password' => 'old-secret123',
            'new_password' => 'new-secret123',
            'new_password_confirmation' => 'new-secret123',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertTrue(Hash::check('new-secret123', $user->password));
    }

    public function test_password_update_fails_with_wrong_current_password(): void
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-secret123'),
        ]);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'username' => $user->username ?? 'validuser2',
            'email' => $user->email,
            'current_password' => 'wrong-secret',
            'new_password' => 'new-secret123',
            'new_password_confirmation' => 'new-secret123',
        ]);

        $response->assertSessionHasErrors(['current_password']);
    }

    public function test_user_can_upload_avatar_file(): void
    {
        Storage::fake('public');

        $user = User::factory()->create([
            'username' => 'avataruser',
        ]);

        $file = UploadedFile::fake()->image('my-photo.jpg', 200, 200);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'avatar_file' => $file,
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertTrue(str_starts_with($user->avatar, 'avatars/'));
        Storage::disk('public')->assertExists($user->avatar);
    }

    public function test_user_can_remove_avatar(): void
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->image('old-avatar.png');
        $storedPath = $file->store('avatars', 'public');

        $user = User::factory()->create([
            'username' => 'removeuser',
            'avatar' => $storedPath,
        ]);

        Storage::disk('public')->assertExists($storedPath);

        $response = $this->actingAs($user)->put(route('profile.update'), [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'remove_avatar' => '1',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $response->assertSessionHas('success');

        $user->refresh();
        $this->assertNull($user->avatar);
        Storage::disk('public')->assertMissing($storedPath);
    }
}
