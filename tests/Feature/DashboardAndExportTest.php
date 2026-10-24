<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_project_metrics_and_upcoming_deadlines(): void
    {
        $user = User::factory()->create();
        $calendar = WorkCalendar::create([
            'name' => 'Kalender Standar',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        $project = Project::create([
            'owner_id' => $user->id,
            'work_calendar_id' => $calendar->id,
            'name' => 'Konser Amal Akbar',
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-30',
            'status' => 'active',
        ]);

        Task::create([
            'project_id' => $project->id,
            'name' => 'Booking Sound System',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
            'duration_days' => 3,
            'status' => 'in_progress',
            'is_critical' => true,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dashboard Utama');
        $response->assertSee('Konser Amal Akbar');
        $response->assertSee('Booking Sound System');
        $response->assertSee('Tugas di Jalur Kritis (CPM)');
    }

    public function test_export_tasks_csv_returns_streamed_csv_with_mitigated_injection(): void
    {
        $user = User::factory()->create();
        $calendar = WorkCalendar::create([
            'name' => 'Kalender Standar',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        $project = Project::create([
            'owner_id' => $user->id,
            'work_calendar_id' => $calendar->id,
            'name' => 'Seminar Teknologi',
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-15',
            'status' => 'planning',
        ]);

        // Uji sanitasi formula injection (nama tugas diawali '=cmd')
        Task::create([
            'project_id' => $project->id,
            'name' => '=SUM(A1:A10)',
            'start_date' => '2026-11-02',
            'end_date' => '2026-11-04',
            'duration_days' => 3,
            'status' => 'todo',
        ]);

        $response = $this->actingAs($user)->get(route('projects.export-tasks', $project));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        // Capture streamed response content
        ob_start();
        $response->sendContent();
        $content = ob_get_clean();

        // Verifikasi ada header kolom CSV
        $this->assertStringContainsString('Nama Tugas', $content);
        $this->assertStringContainsString('Jalur Kritis (CPM)', $content);

        // Verifikasi bahwa karakter formula disanitasi dengan tanda petik tunggal '
        $this->assertStringContainsString("'=SUM(A1:A10)", $content);
    }

    public function test_admin_dashboard_shows_global_statistics(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $regularUser = User::factory()->create(['role' => 'user']);

        $calendar = WorkCalendar::create([
            'name' => 'Kalender Standar',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        Project::create([
            'owner_id' => $regularUser->id,
            'work_calendar_id' => $calendar->id,
            'name' => 'Proyek Global User Lain',
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-30',
            'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Admin Overview');
        $response->assertSee('Ringkasan Sistem dan Global');
        $response->assertSee('Total Pengguna Terdaftar');
        $response->assertSee('Total Proyek Global');
        $response->assertSee('Buka Kelola Pengguna');
    }
}
