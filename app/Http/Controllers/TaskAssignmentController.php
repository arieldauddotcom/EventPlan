<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Resource;
use App\Models\Task;
use App\Models\TaskAssignment;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TaskAssignmentController extends Controller
{
    public function store(Request $request, Project $project, Task $task)
    {
        $this->authorize('update', $project);

        if ($task->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'resource_id' => 'required|exists:resources,id',
            'allocation_percentage' => 'required|integer|min:1|max:100',
        ], [
            'resource_id.required' => 'Pilih anggota tim yang ingin ditugaskan.',
            'allocation_percentage.required' => 'Persentase alokasi beban kerja wajib diisi.',
            'allocation_percentage.min' => 'Alokasi minimal 1%.',
            'allocation_percentage.max' => 'Alokasi maksimal 100%.',
        ]);

        $resource = Resource::findOrFail($validated['resource_id']);
        if ($resource->project_id !== $project->id) {
            abort(404);
        }

        // Cek konflik cuti / ketidakhadiran
        $taskStart = Carbon::parse($task->start_date);
        $taskEnd = Carbon::parse($task->end_date);

        $hasLeaveConflict = $resource->leaves()
            ->where(function ($q) use ($taskStart, $taskEnd) {
                $q->whereBetween('start_date', [$taskStart, $taskEnd])
                  ->orWhereBetween('end_date', [$taskStart, $taskEnd])
                  ->orWhere(function ($sub) use ($taskStart, $taskEnd) {
                      $sub->where('start_date', '<=', $taskStart)
                          ->where('end_date', '>=', $taskEnd);
                  });
            })
            ->exists();

        $warningMessage = null;
        if ($hasLeaveConflict) {
            $warningMessage = 'Peringatan: Anggota memiliki jadwal cuti/tidak hadir pada rentang waktu tugas ini!';
        }

        TaskAssignment::updateOrCreate([
            'task_id' => $task->id,
            'resource_id' => $resource->id,
        ], [
            'allocation_percentage' => (int) $validated['allocation_percentage'],
        ]);

        $redirect = back()->with('success', 'Penugasan anggota tim berhasil disimpan.');
        if ($warningMessage) {
            $redirect->with('warning', $warningMessage);
        }

        return $redirect;
    }

    public function destroy(Project $project, Task $task, TaskAssignment $assignment)
    {
        $this->authorize('update', $project);

        if ($assignment->task_id !== $task->id || $task->project_id !== $project->id) {
            abort(404);
        }

        $assignment->delete();

        return back()->with('success', 'Penugasan anggota tim berhasil dihapus.');
    }
}
