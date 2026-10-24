<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Resource;
use App\Models\Task;
use App\Models\User;
use App\Models\WorkCalendar;
use App\Services\DependencyValidator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskAndResourceManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Project $project;
    protected WorkCalendar $calendar;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->calendar = WorkCalendar::create([
            'name' => 'Kalender Uji Coba',
            'working_days' => [1, 2, 3, 4, 5],
            'hours_per_day' => 8,
        ]);

        $this->project = Project::create([
            'owner_id' => $this->user->id,
            'work_calendar_id' => $this->calendar->id,
            'name' => 'Festival Musik Kampus',
            'start_date' => '2026-11-02', // Senin
            'deadline' => '2026-11-30',
            'status' => 'planning',
        ]);
    }

    public function test_user_can_create_task_and_end_date_is_calculated(): void
    {
        // Durasi 5 hari kerja mulai Senin 2026-11-02 -> Selesai Jumat 2026-11-06
        $response = $this->actingAs($this->user)->post("/projects/{$this->project->id}/tasks", [
            'name' => 'Penyusunan Anggaran',
            'start_date' => '2026-11-02',
            'duration_days' => 5,
            'priority' => 'high',
            'status' => 'todo',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('tasks', [
            'project_id' => $this->project->id,
            'name' => 'Penyusunan Anggaran',
            'start_date' => '2026-11-02 00:00:00',
            'end_date' => '2026-11-06 00:00:00',
            'duration_days' => 5,
        ]);
    }

    public function test_dependency_cycle_detection_prevents_circular_dependencies(): void
    {
        $taskA = Task::create([
            'project_id' => $this->project->id,
            'name' => 'Tugas A',
            'start_date' => '2026-11-02',
            'end_date' => '2026-11-04',
            'duration_days' => 3,
        ]);

        $taskB = Task::create([
            'project_id' => $this->project->id,
            'name' => 'Tugas B',
            'start_date' => '2026-11-05',
            'end_date' => '2026-11-06',
            'duration_days' => 2,
        ]);

        // A -> B (A predecessor dari B)
        $this->actingAs($this->user)->post("/projects/{$this->project->id}/tasks/{$taskB->id}/dependencies", [
            'predecessor_id' => $taskA->id,
            'type' => 'FS',
        ]);

        $this->assertDatabaseHas('task_dependencies', [
            'predecessor_id' => $taskA->id,
            'successor_id' => $taskB->id,
        ]);

        // Coba tambahkan B -> A (harus ditolak karena membentuk siklus)
        $response = $this->actingAs($this->user)->post("/projects/{$this->project->id}/tasks/{$taskA->id}/dependencies", [
            'predecessor_id' => $taskB->id,
            'type' => 'FS',
        ]);

        $response->assertSessionHasErrors('predecessor_id');
        $this->assertDatabaseMissing('task_dependencies', [
            'predecessor_id' => $taskB->id,
            'successor_id' => $taskA->id,
        ]);
    }

    public function test_resource_assignment_and_leave_conflict_detection(): void
    {
        $resource = Resource::create([
            'project_id' => $this->project->id,
            'name' => 'Budi Santoso',
            'role' => 'Logistik',
            'cost_per_hour' => 45000,
        ]);

        // Cuti pada rentang 2026-11-03 s/d 2026-11-04
        $resource->leaves()->create([
            'start_date' => '2026-11-03',
            'end_date' => '2026-11-04',
            'reason' => 'Izin',
        ]);

        $task = Task::create([
            'project_id' => $this->project->id,
            'name' => 'Pengadaan Tenda',
            'start_date' => '2026-11-02',
            'end_date' => '2026-11-05',
            'duration_days' => 4,
        ]);

        // Tugaskan Budi ke tugas yang bertabrakan dengan jadwal cutinya
        $response = $this->actingAs($this->user)->post("/projects/{$this->project->id}/tasks/{$task->id}/assignments", [
            'resource_id' => $resource->id,
            'allocation_percentage' => 100,
        ]);

        $response->assertSessionHas('warning'); // Ada pesan peringatan konflik
        $this->assertDatabaseHas('task_assignments', [
            'task_id' => $task->id,
            'resource_id' => $resource->id,
            'allocation_percentage' => 100,
        ]);
    }

    public function test_leader_can_render_task_edit_page_and_update_task(): void
    {
        $task = Task::create([
            'project_id' => $this->project->id,
            'name' => 'Tugas Awal',
            'start_date' => '2026-11-02',
            'end_date' => '2026-11-04',
            'duration_days' => 3,
            'priority' => 'low',
            'status' => 'todo',
            'progress_percentage' => 10,
        ]);

        // Cek halaman edit tugas
        $editResponse = $this->actingAs($this->user)->get(route('projects.tasks.edit', [$this->project, $task]));
        $editResponse->assertStatus(200);
        $editResponse->assertSee('Edit Tugas');
        $editResponse->assertSee('Tugas Awal');

        // Update tugas
        $updateResponse = $this->actingAs($this->user)->put(route('projects.tasks.update', [$this->project, $task]), [
            'name' => 'Tugas Telah Diperbarui',
            'start_date' => '2026-11-03',
            'duration_days' => 4,
            'priority' => 'urgent',
            'status' => 'in_progress',
            'progress_percentage' => 45,
            'description' => 'Deskripsi baru tugas yang diedit',
        ]);

        $updateResponse->assertRedirect(route('projects.show', $this->project));
        $updateResponse->assertSessionHas('success');

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'name' => 'Tugas Telah Diperbarui',
            'duration_days' => 4,
            'priority' => 'urgent',
            'status' => 'in_progress',
            'progress_percentage' => 45,
        ]);
    }
}
