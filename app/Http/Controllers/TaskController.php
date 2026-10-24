<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskDependency;
use App\Services\DependencyValidator;
use App\Services\WorkingDaysCalculator;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    protected DependencyValidator $validator;

    public function __construct(DependencyValidator $validator)
    {
        $this->validator = $validator;
    }

    public function store(Request $request, Project $project)
    {
        // Hanya Project Leader yang boleh membuat tugas baru
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:tasks,id',
            'start_date' => 'required|date',
            'duration_days' => 'required|integer|min:1|max:365',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:todo,in_progress,review,completed',
            'progress_percentage' => 'nullable|integer|between:0,100',
        ], [
            'name.required' => 'Nama tugas wajib diisi.',
            'start_date.required' => 'Tanggal mulai wajib diisi.',
            'duration_days.min' => 'Durasi minimal 1 hari kerja.',
        ]);

        $calc = new WorkingDaysCalculator($project->workCalendar);
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = $calc->calculateEndDate($startDate, (int) $validated['duration_days']);

        $task = $project->tasks()->create([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'duration_days' => (int) $validated['duration_days'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'progress_percentage' => (int) ($validated['progress_percentage'] ?? 0),
        ]);

        return back()->with('success', 'Tugas "' . $task->name . '" berhasil ditambahkan.');
    }

    public function edit(Project $project, Task $task)
    {
        $this->authorize('update', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $otherTasks = $project->tasks()->where('id', '!=', $task->id)->get();

        return view('projects.tasks.edit', compact('project', 'task', 'otherTasks'));
    }

    public function update(Request $request, Project $project, Task $task)
    {
        $user = Auth::user();

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $isLeader = $project->isLeader($user);

        // Jika bukan Leader, cek apakah Member yang ditugaskan (assigned) pada tugas ini
        if (!$isLeader) {
            $isAssignedMember = $project->isMember($user) && $task->assignments()->whereHas('resource', function ($query) use ($user) {
                $query->where('email', strtolower($user->email));
            })->exists();

            if (!$isAssignedMember) {
                abort(403, 'Anda hanya dapat memperbarui progres tugas yang diberikan kepada Anda.');
            }

            // Member hanya boleh memperbarui progress_percentage dan status
            $validated = $request->validate([
                'status' => 'required|in:todo,in_progress,review,completed',
                'progress_percentage' => 'required|integer|between:0,100',
            ]);

            $task->update([
                'status' => $validated['status'],
                'progress_percentage' => (int) $validated['progress_percentage'],
            ]);

            return back()->with('success', 'Progres tugas berhasil diperbarui.');
        }

        // Jika Leader: boleh edit seluruh atribut tugas
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:tasks,id',
            'start_date' => 'required|date',
            'duration_days' => 'required|integer|min:1|max:365',
            'priority' => 'required|in:low,medium,high,urgent',
            'status' => 'required|in:todo,in_progress,review,completed',
            'progress_percentage' => 'nullable|integer|between:0,100',
        ]);

        if (!empty($validated['parent_id']) && (int) $validated['parent_id'] === $task->id) {
            return back()->withErrors(['parent_id' => 'Tugas tidak dapat menjadi parent untuk dirinya sendiri.']);
        }

        $calc = new WorkingDaysCalculator($project->workCalendar);
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = $calc->calculateEndDate($startDate, (int) $validated['duration_days']);

        $task->update([
            'parent_id' => $validated['parent_id'] ?? null,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
            'duration_days' => (int) $validated['duration_days'],
            'priority' => $validated['priority'],
            'status' => $validated['status'],
            'progress_percentage' => (int) ($validated['progress_percentage'] ?? 0),
        ]);

        return redirect()->route('projects.show', $project)->with('success', 'Data tugas berhasil diperbarui.');
    }

    public function destroy(Project $project, Task $task)
    {
        $this->authorize('update', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $task->delete();

        return back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function addDependency(Request $request, Project $project, Task $task)
    {
        $this->authorize('update', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'predecessor_id' => 'required|exists:tasks,id',
            'type' => 'required|in:FS,SS',
            'lag_days' => 'nullable|integer|min:0',
        ]);

        $predecessorId = (int) $validated['predecessor_id'];

        if ($predecessorId === $task->id) {
            return back()->withErrors(['predecessor_id' => 'Tugas tidak dapat bergantung pada dirinya sendiri.']);
        }

        // Cek deteksi siklus ketergantungan (Cycle Detection)
        if ($this->validator->createsCycle($predecessorId, $task->id)) {
            return back()->withErrors([
                'predecessor_id' => 'Gagal menambahkan ketergantungan! Terdeteksi siklus melingkar (Circular Dependency).',
            ]);
        }

        TaskDependency::firstOrCreate([
            'predecessor_id' => $predecessorId,
            'successor_id' => $task->id,
        ], [
            'type' => $validated['type'],
            'lag_days' => (int) ($validated['lag_days'] ?? 0),
        ]);

        // Sesuaikan tanggal tugas jika melanggar predecessor constraint
        $calc = new WorkingDaysCalculator($project->workCalendar);
        $minStart = $this->validator->calculateEarliestStart($task, $calc);

        if ($minStart->gt(Carbon::parse($task->start_date))) {
            $endDate = $calc->calculateEndDate($minStart, $task->duration_days);
            $task->update([
                'start_date' => $minStart->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]);
        }

        return back()->with('success', 'Ketergantungan tugas berhasil ditambahkan.');
    }

    public function removeDependency(Project $project, Task $task, TaskDependency $dependency)
    {
        $this->authorize('update', $project);

        if ($dependency->successor_id !== $task->id || $task->project_id !== $project->id) {
            abort(404);
        }

        $dependency->delete();

        return back()->with('success', 'Ketergantungan tugas berhasil dihapus.');
    }
}
