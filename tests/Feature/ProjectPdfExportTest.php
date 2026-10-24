<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPdfExportTest extends TestCase
{
    use RefreshDatabase;

    protected User $leader;
    protected User $member;
    protected User $outsider;
    protected Project $project;

    protected function setUp(): void
    {
        parent::setUp();

        $this->leader = User::factory()->create([
            'name' => 'Leader User',
            'email' => 'leader@example.com',
            'role' => 'user',
        ]);

        $this->member = User::factory()->create([
            'name' => 'Member User',
            'email' => 'member@example.com',
            'role' => 'user',
        ]);

        $this->outsider = User::factory()->create([
            'name' => 'Outsider User',
            'email' => 'outsider@example.com',
            'role' => 'user',
        ]);

        $calendar = WorkCalendar::create([
            'name' => 'Kalender Standar Proyek',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        $this->project = Project::create([
            'name' => 'Konser Musik Festival 2026',
            'organization' => 'BEM Kampus',
            'owner_id' => $this->leader->id,
            'work_calendar_id' => $calendar->id,
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-30',
            'status' => 'planning',
            'description' => 'Persiapan festival musik tahunan.',
        ]);

        // Leader role per project
        $this->project->members()->create([
            'user_id' => $this->leader->id,
            'role' => 'leader',
        ]);

        // Member role per project
        $this->project->members()->create([
            'user_id' => $this->member->id,
            'role' => 'member',
        ]);

        // Tambah beberapa tugas
        $taskA = Task::create([
            'project_id' => $this->project->id,
            'name' => 'Perizinan Tempat',
            'start_date' => '2026-11-02',
            'end_date' => '2026-11-06',
            'duration_days' => 5,
            'priority' => 'high',
            'status' => 'completed',
            'progress_percentage' => 100,
        ]);

        $taskB = Task::create([
            'project_id' => $this->project->id,
            'name' => 'Pemasangan Panggung & Audio',
            'start_date' => '2026-11-09',
            'end_date' => '2026-11-13',
            'duration_days' => 5,
            'priority' => 'urgent',
            'status' => 'in_progress',
            'progress_percentage' => 40,
        ]);
    }

    public function test_guest_cannot_export_project_pdf(): void
    {
        $response = $this->get(route('projects.export-pdf', $this->project));
        $response->assertRedirect('/login');
    }

    public function test_outsider_cannot_export_project_pdf(): void
    {
        $response = $this->actingAs($this->outsider)->get(route('projects.export-pdf', $this->project));
        $response->assertStatus(403);
    }

    public function test_leader_can_export_project_pdf(): void
    {
        $response = $this->actingAs($this->leader)->get(route('projects.export-pdf', $this->project));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('attachment', $response->headers->get('content-disposition'));
        $this->assertStringContainsString('laporan_proyek_konser-musik-festival-2026_', $response->headers->get('content-disposition'));
    }

    public function test_user_can_stream_pdf_preview(): void
    {
        $response = $this->actingAs($this->leader)->get(route('projects.export-pdf', [
            'project' => $this->project,
            'preview' => 1,
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('inline', $response->headers->get('content-disposition'));
    }

    public function test_user_can_filter_pdf_export_by_date_range(): void
    {
        $response = $this->actingAs($this->leader)->get(route('projects.export-pdf', [
            'project' => $this->project,
            'start_date' => '2026-11-05',
            'end_date' => '2026-11-20',
        ]));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_member_can_export_project_pdf(): void
    {
        $response = $this->actingAs($this->member)->get(route('projects.export-pdf', $this->project));

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('laporan_proyek_konser-musik-festival-2026_', $response->headers->get('content-disposition'));
    }
}
