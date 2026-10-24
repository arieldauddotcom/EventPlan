<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class EmailPasswordAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_with_email_and_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Siti Rahma',
            'username' => 'sitirahma',
            'email' => 'siti@event.com',
            'password' => Hash::make('rahasia123'),
        ]);

        $response = $this->post(route('login.perform'), [
            'email' => 'siti@event.com',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_username_and_password(): void
    {
        $user = User::factory()->create([
            'name' => 'Budi Santoso',
            'username' => 'budisantoso',
            'email' => 'budi@event.com',
            'password' => Hash::make('rahasia123'),
        ]);

        $response = $this->post(route('login.perform'), [
            'email' => 'budisantoso',
            'password' => 'rahasia123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'user_test@event.com',
            'password' => Hash::make('password_benar'),
        ]);

        $response = $this->post(route('login.perform'), [
            'email' => 'user_test@event.com',
            'password' => 'password_salah',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
