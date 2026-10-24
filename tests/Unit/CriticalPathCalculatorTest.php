<?php

namespace Tests\Unit;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Models\User;
use App\Models\WorkCalendar;
use App\Services\CriticalPathCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CriticalPathCalculatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_cpm_forward_and_backward_pass_calculation(): void
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
            'name' => 'Konser Musik 2026',
            'start_date' => '2026-11-01',
            'deadline' => '2026-11-30',
            'status' => 'planning',
        ]);

        // Skenario CPM standar:
        // A (durasi 3) -> B (durasi 4) -> D (durasi 2)
        //              -> C (durasi 2) -> D (durasi 2)
        // Jalur 1: A + B + D = 3 + 4 + 2 = 9 (Critical Path)
        // Jalur 2: A + C + D = 3 + 2 + 2 = 7 (Float pada C = 2)

        $taskA = Task::create([
            'project_id' => $project->id,
            'name' => 'A: Sewa Venue',
            'start_date' => '2026-11-01',
            'end_date' => '2026-11-03',
            'duration_days' => 3,
            'status' => 'todo',
            'priority' => 'high',
        ]);

        $taskB = Task::create([
            'project_id' => $project->id,
            'name' => 'B: Panggung & Sound',
            'start_date' => '2026-11-04',
            'end_date' => '2026-11-07',
            'duration_days' => 4,
            'status' => 'todo',
            'priority' => 'high',
        ]);

        $taskC = Task::create([
            'project_id' => $project->id,
            'name' => 'C: Cetak Tiket',
            'start_date' => '2026-11-04',
            'end_date' => '2026-11-05',
            'duration_days' => 2,
            'status' => 'todo',
            'priority' => 'medium',
        ]);

        $taskD = Task::create([
            'project_id' => $project->id,
            'name' => 'D: Gladi Bersih',
            'start_date' => '2026-11-08',
            'end_date' => '2026-11-09',
            'duration_days' => 2,
            'status' => 'todo',
            'priority' => 'urgent',
        ]);

        // Dependencies: A -> B, A -> C, B -> D, C -> D
        TaskDependency::create([
            'predecessor_id' => $taskA->id,
            'successor_id' => $taskB->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        TaskDependency::create([
            'predecessor_id' => $taskA->id,
            'successor_id' => $taskC->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        TaskDependency::create([
            'predecessor_id' => $taskB->id,
            'successor_id' => $taskD->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        TaskDependency::create([
            'predecessor_id' => $taskC->id,
            'successor_id' => $taskD->id,
            'type' => 'FS',
            'lag_days' => 0,
        ]);

        $calculator = new CriticalPathCalculator();
        $result = $calculator->calculate($project);

        $this->assertEquals(9, $result['project_duration']);

        $taskA->refresh();
        $taskB->refresh();
        $taskC->refresh();
        $taskD->refresh();

        // Task A: ES 0, EF 3, LS 0, LF 3, Float 0 -> Kritis
        $this->assertEquals(0, $taskA->early_start);
        $this->assertEquals(3, $taskA->early_finish);
        $this->assertEquals(0, $taskA->total_float);
        $this->assertTrue((bool)$taskA->is_critical);

        // Task B: ES 3, EF 7, LS 3, LF 7, Float 0 -> Kritis
        $this->assertEquals(3, $taskB->early_start);
        $this->assertEquals(7, $taskB->early_finish);
        $this->assertEquals(0, $taskB->total_float);
        $this->assertTrue((bool)$taskB->is_critical);

        // Task C: ES 3, EF 5, LS 5, LF 7, Float 2 -> Bukan kritis
        $this->assertEquals(3, $taskC->early_start);
        $this->assertEquals(5, $taskC->early_finish);
        $this->assertEquals(2, $taskC->total_float);
        $this->assertFalse((bool)$taskC->is_critical);

        // Task D: ES 7, EF 9, LS 7, LF 9, Float 0 -> Kritis
        $this->assertEquals(7, $taskD->early_start);
        $this->assertEquals(9, $taskD->early_finish);
        $this->assertEquals(0, $taskD->total_float);
        $this->assertTrue((bool)$taskD->is_critical);
    }
}
