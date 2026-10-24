<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\CriticalPathCalculator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class VisualizationController extends Controller
{
    protected CriticalPathCalculator $cpmCalculator;

    public function __construct(CriticalPathCalculator $cpmCalculator)
    {
        $this->cpmCalculator = $cpmCalculator;
    }

    /**
     * Tampilan Gantt Chart interaktif
     */
    public function gantt(Project $project)
    {
        $this->authorize('view', $project);

        $cpm = $this->cpmCalculator->calculate($project);

        $tasks = $project->tasks()
            ->with(['predecessors', 'successors', 'resources', 'assignments'])
            ->orderBy('start_date')
            ->get();

        // Rentang waktu Gantt Chart (Proyek start s/d deadline / max task end)
        $minDate = $project->start_date->copy();
        $maxDate = $project->deadline->copy();

        foreach ($tasks as $t) {
            if ($t->start_date->lt($minDate)) {
                $minDate = $t->start_date->copy();
            }
            if ($t->end_date->gt($maxDate)) {
                $maxDate = $t->end_date->copy();
            }
        }

        $period = CarbonPeriod::create($minDate, $maxDate);
        $totalDays = max(1, $period->count());

        return view('projects.visualizations.gantt', compact('project', 'tasks', 'cpm', 'minDate', 'maxDate', 'period', 'totalDays'));
    }

    /**
     * Tampilan PERT / Network Diagram & CPM
     */
    public function pert(Project $project)
    {
        $this->authorize('view', $project);

        $cpm = $this->cpmCalculator->calculate($project);

        $tasks = $project->tasks()
            ->with(['predecessors', 'successors', 'predecessorDependencies'])
            ->orderBy('early_start')
            ->get();

        return view('projects.visualizations.pert', compact('project', 'tasks', 'cpm'));
    }

    /**
     * Tampilan Resource Workload & Capacity Chart
     */
    public function resources(Project $project)
    {
        $this->authorize('view', $project);

        $resources = $project->resources()
            ->with(['leaves', 'assignments.task'])
            ->get();

        $minDate = $project->start_date->copy();
        $maxDate = $project->deadline->copy();
        $days = [];

        $current = $minDate->copy();
        while ($current->lte($maxDate)) {
            $days[] = $current->copy();
            $current->addDay();
        }

        // Kalkulasi alokasi harian per resource
        $resourceWorkloads = [];
        foreach ($resources as $res) {
            $dailyAllocation = [];

            foreach ($days as $d) {
                $dateStr = $d->format('Y-m-d');
                $isLeave = $res->leaves->some(function ($lv) use ($d) {
                    return $d->betweenIncluded($lv->start_date, $lv->end_date);
                });

                $totalLoad = 0;
                $activeTasks = [];

                if (!$isLeave) {
                    foreach ($res->assignments as $asg) {
                        $task = $asg->task;
                        if ($task && $d->betweenIncluded($task->start_date, $task->end_date)) {
                            $totalLoad += (int) $asg->allocation_percentage;
                            $activeTasks[] = [
                                'task' => $task->name,
                                'allocation' => $asg->allocation_percentage,
                            ];
                        }
                    }
                }

                $dailyAllocation[$dateStr] = [
                    'date' => $d,
                    'is_leave' => $isLeave,
                    'total_load' => $totalLoad,
                    'is_overallocated' => ($totalLoad > 100),
                    'active_tasks' => $activeTasks,
                ];
            }

            $resourceWorkloads[$res->id] = [
                'resource' => $res,
                'daily' => $dailyAllocation,
            ];
        }

        return view('projects.visualizations.resources', compact('project', 'resources', 'days', 'resourceWorkloads'));
    }
}
