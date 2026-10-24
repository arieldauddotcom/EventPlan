<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Resource;
use App\Models\Task;
use App\Models\TaskAssignment;
use App\Models\User;
use App\Models\WorkCalendar;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisualizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_gantt_pert_and_resource_views(): void
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
            'name' => 'Festival Musik Tahunan',
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-20',
            'status' => 'planning',
        ]);

        $task = Task::create([
            'project_id' => $project->id,
            'name' => 'Promosi Media Sosial',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-05',
            'duration_days' => 5,
            'status' => 'in_progress',
            'priority' => 'high',
        ]);

        $resource = Resource::create([
            'project_id' => $project->id,
            'name' => 'Budi Soundman',
            'role' => 'Audio Engineer',
            'email' => 'budi@event.com',
            'cost_per_hour' => 150000,
        ]);

        TaskAssignment::create([
            'task_id' => $task->id,
            'resource_id' => $resource->id,
            'allocation_percentage' => 80,
        ]);

        // 1. Cek akses Gantt Chart
        $ganttResponse = $this->actingAs($user)->get(route('projects.gantt', $project));
        $ganttResponse->assertStatus(200);
        $ganttResponse->assertSee('Gantt Chart Timeline');
        $ganttResponse->assertSee('Promosi Media Sosial');

        // 2. Cek akses PERT Diagram
        $pertResponse = $this->actingAs($user)->get(route('projects.pert', $project));
        $pertResponse->assertStatus(200);
        $pertResponse->assertSee('PERT / Network Diagram');
        $pertResponse->assertSee('Promosi Media Sosial');

        // 3. Cek akses Resource Workload Chart
        $resourceResponse = $this->actingAs($user)->get(route('projects.resources-chart', $project));
        $resourceResponse->assertStatus(200);
        $resourceResponse->assertSee('Resource Workload');
        $resourceResponse->assertSee('Budi Soundman');
        $resourceResponse->assertSee('80%');
    }
}
