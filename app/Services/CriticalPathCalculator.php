<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;

class CriticalPathCalculator
{
    /**
     * Menjalankan kalkulasi CPM (Forward pass & Backward pass) untuk semua tugas pada proyek.
     * Mengembalikan ringkasan: [
     *   'tasks' => Collection,
     *   'project_duration' => int,
     *   'critical_tasks' => Collection,
     *   'critical_path_names' => array
     * ]
     */
    public function calculate(Project $project): array
    {
        $tasks = $project->tasks()->with(['predecessorDependencies', 'successorDependencies'])->get();

        if ($tasks->isEmpty()) {
            return [
                'tasks' => collect([]),
                'project_duration' => 0,
                'critical_tasks' => collect([]),
                'critical_path_names' => [],
            ];
        }

        $taskMap = [];
        $inDegree = [];
        $adj = [];
        $revAdj = [];

        foreach ($tasks as $t) {
            $taskMap[$t->id] = $t;
            $inDegree[$t->id] = 0;
            $adj[$t->id] = [];
            $revAdj[$t->id] = [];
        }

        // Ambil dependencies
        $taskIds = $tasks->pluck('id')->toArray();
        $dependencies = TaskDependency::whereIn('predecessor_id', $taskIds)
            ->whereIn('successor_id', $taskIds)
            ->get();

        foreach ($dependencies as $dep) {
            $u = $dep->predecessor_id;
            $v = $dep->successor_id;

            $adj[$u][] = [
                'to' => $v,
                'type' => $dep->type,
                'lag' => (int) $dep->lag_days,
            ];

            $revAdj[$v][] = [
                'from' => $u,
                'type' => $dep->type,
                'lag' => (int) $dep->lag_days,
            ];

            $inDegree[$v]++;
        }

        // 1. FORWARD PASS (Kahn's Topological Sort)
        // ES(i) = max(EF(predecessors) + lag)
        // EF(i) = ES(i) + Duration
        $es = [];
        $ef = [];
        $queue = [];

        foreach ($tasks as $t) {
            $es[$t->id] = 0;
            $ef[$t->id] = $t->duration_days;
            if ($inDegree[$t->id] === 0) {
                $queue[] = $t->id;
            }
        }

        $topologicalOrder = [];

        while (!empty($queue)) {
            $curr = array_shift($queue);
            $topologicalOrder[] = $curr;
            $t = $taskMap[$curr];

            foreach ($adj[$curr] as $edge) {
                $next = $edge['to'];
                $type = $edge['type'];
                $lag = $edge['lag'];

                if ($type === 'FS') {
                    $potentialES = $ef[$curr] + $lag;
                } else { // 'SS'
                    $potentialES = $es[$curr] + $lag;
                }

                if ($potentialES > $es[$next]) {
                    $es[$next] = $potentialES;
                    $ef[$next] = $es[$next] + $taskMap[$next]->duration_days;
                }

                $inDegree[$next]--;
                if ($inDegree[$next] === 0) {
                    $queue[] = $next;
                }
            }
        }

        // Durasi total proyek = Max(EF semua tugas)
        $projectDuration = 0;
        foreach ($tasks as $t) {
            if ($ef[$t->id] > $projectDuration) {
                $projectDuration = $ef[$t->id];
            }
        }

        // 2. BACKWARD PASS
        // LF(i) = min(LS(successors) - lag)
        // LS(i) = LF(i) - Duration
        $lf = [];
        $ls = [];

        foreach ($tasks as $t) {
            $lf[$t->id] = $projectDuration;
            $ls[$t->id] = $projectDuration - $t->duration_days;
        }

        // Iterasi urutan topologis terbalik
        $reverseOrder = array_reverse($topologicalOrder);

        foreach ($reverseOrder as $curr) {
            $t = $taskMap[$curr];

            if (!empty($adj[$curr])) {
                $minLF = $projectDuration;
                foreach ($adj[$curr] as $edge) {
                    $next = $edge['to'];
                    $type = $edge['type'];
                    $lag = $edge['lag'];

                    if ($type === 'FS') {
                        $candidateLF = $ls[$next] - $lag;
                    } else { // 'SS'
                        // LS(next) >= ES(curr) + lag -> LS(curr) <= LS(next) - lag
                        $candidateLF = ($ls[$next] - $lag) + $t->duration_days;
                    }

                    if ($candidateLF < $minLF) {
                        $minLF = $candidateLF;
                    }
                }
                $lf[$curr] = $minLF;
                $ls[$curr] = $lf[$curr] - $t->duration_days;
            }
        }

        // 3. TOTAL FLOAT & CRITICAL PATH
        // Total Float = LS - ES (atau LF - EF)
        // Critical jika Total Float <= 0
        $criticalTasks = collect([]);

        foreach ($tasks as $t) {
            $tFloat = $ls[$t->id] - $es[$t->id];
            $isCritical = ($tFloat <= 0);

            $t->early_start = $es[$t->id];
            $t->early_finish = $ef[$t->id];
            $t->late_start = $ls[$t->id];
            $t->late_finish = $lf[$t->id];
            $t->total_float = $tFloat;
            $t->is_critical = $isCritical;

            // Simpan cache hasil kalkulasi CPM ke database
            $t->saveQuietly();

            if ($isCritical) {
                $criticalTasks->push($t);
            }
        }

        return [
            'tasks' => $tasks,
            'project_duration' => $projectDuration,
            'critical_tasks' => $criticalTasks,
            'critical_path_names' => $criticalTasks->pluck('name')->toArray(),
        ];
    }
}
