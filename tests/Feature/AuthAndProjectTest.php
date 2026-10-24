<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Project;
use App\Models\WorkCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class AuthAndProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_to_login_for_guest(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('EventPlan');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('EventPlan');
        $response->assertSee('Masuk dengan Google');
    }

    public function test_dev_login_works_in_local_environment(): void
    {
        $response = $this->post('/auth/dev-login', [
            'email' => 'ariel@example.com',
            'name' => 'Mohamad Nazril R. Daud',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', ['email' => 'ariel@example.com']);
    }

    public function test_google_oauth_callback_creates_user_and_logs_in(): void
    {
        $abstractUser = Mockery::mock(SocialiteUser::class);
        $abstractUser->shouldReceive('getId')->andReturn('google-123456');
        $abstractUser->shouldReceive('getName')->andReturn('Test User Google');
        $abstractUser->shouldReceive('getEmail')->andReturn('user@google.com');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/test');

        $provider = Mockery::mock('Laravel\Socialite\Two\GoogleProvider');
        $provider->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

        $response = $this->get('/auth/google/callback');
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        $this->assertDatabaseHas('users', [
            'email' => 'user@google.com',
            'google_id' => 'google-123456',
        ]);
    }

    public function test_authenticated_user_can_access_dashboard_and_create_project(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'Seminar Teknologi AI 2026',
            'organization' => 'Himpunan Mahasiswa',
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-15',
            'status' => 'planning',
            'description' => 'Seminar nasional tentang agen cerdas.',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('projects', [
            'name' => 'Seminar Teknologi AI 2026',
            'owner_id' => $user->id,
        ]);
        $this->assertDatabaseHas('work_calendars', [
            'name' => 'Kalender - Seminar Teknologi AI 2026',
        ]);
    }

    public function test_project_isolation_user_cannot_access_other_users_private_project(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $calendar = WorkCalendar::create([
            'name' => 'Kalender A',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        $project = Project::create([
            'name' => 'Rahasia Owner',
            'owner_id' => $owner->id,
            'work_calendar_id' => $calendar->id,
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-10',
            'status' => 'planning',
        ]);

        // Attempt by other user should be 403 Forbidden
        $response = $this->actingAs($otherUser)->get('/projects/' . $project->id);
        $response->assertStatus(403);

        // Owner can access
        $responseOwner = $this->actingAs($owner)->get('/projects/' . $project->id);
        $responseOwner->assertStatus(200);
        $responseOwner->assertSee('Rahasia Owner');
    }

    public function test_leader_can_render_edit_page_and_update_project(): void
    {
        $owner = User::factory()->create();
        $calendar = WorkCalendar::create([
            'name' => 'Kalender Edit',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        $project = Project::create([
            'name' => 'Proyek Sebelum Edit',
            'owner_id' => $owner->id,
            'work_calendar_id' => $calendar->id,
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-15',
            'status' => 'planning',
        ]);

        $editPageResponse = $this->actingAs($owner)->get(route('projects.edit', $project));
        $editPageResponse->assertStatus(200);
        $editPageResponse->assertSee('Edit Proyek');
        $editPageResponse->assertSee('Proyek Sebelum Edit');

        $updateResponse = $this->actingAs($owner)->put(route('projects.update', $project), [
            'name' => 'Proyek Sesudah Edit Sukses',
            'organization' => 'Yayasan Gemilang',
            'start_date' => '2026-11-02',
            'deadline' => '2026-11-20',
            'status' => 'active',
            'work_calendar_id' => $calendar->id,
            'description' => 'Deskripsi baru setelah diedit',
        ]);

        $updateResponse->assertRedirect(route('projects.show', $project));
        $this->assertDatabaseHas('projects', [
            'id' => $project->id,
            'name' => 'Proyek Sesudah Edit Sukses',
            'organization' => 'Yayasan Gemilang',
            'status' => 'active',
        ]);
    }
}
