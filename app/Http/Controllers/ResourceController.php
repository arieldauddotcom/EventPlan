<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Resource;
use App\Models\ResourceLeave;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|string|max:100',
            'cost_per_hour' => 'nullable|numeric|min:0',
            'max_hours_per_day' => 'nullable|numeric|min:1|max:24',
        ], [
            'name.required' => 'Nama anggota tim wajib diisi.',
            'role.required' => 'Peran atau jabatan wajib diisi.',
        ]);

        $project->resources()->create([
            'name' => $validated['name'],
            'email' => $validated['email'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'role' => $validated['role'],
            'cost_per_hour' => $validated['cost_per_hour'] ?? 0.00,
            'max_hours_per_day' => $validated['max_hours_per_day'] ?? 8.0,
        ]);

        return back()->with('success', 'Anggota tim berhasil ditambahkan.');
    }

    public function update(Request $request, Project $project, Resource $resource)
    {
        $this->authorize('update', $project);

        if ($resource->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'role' => 'required|string|max:100',
            'cost_per_hour' => 'nullable|numeric|min:0',
            'max_hours_per_day' => 'nullable|numeric|min:1|max:24',
        ]);

        $resource->update($validated);

        return back()->with('success', 'Data anggota tim berhasil diperbarui.');
    }

    public function destroy(Project $project, Resource $resource)
    {
        $this->authorize('update', $project);

        if ($resource->project_id !== $project->id) {
            abort(404);
        }

        $resource->delete();

        return back()->with('success', 'Anggota tim berhasil dihapus dari proyek.');
    }

    public function addLeave(Request $request, Project $project, Resource $resource)
    {
        $this->authorize('update', $project);

        if ($resource->project_id !== $project->id) {
            abort(404);
        }

        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'nullable|string|max:255',
        ], [
            'start_date.required' => 'Tanggal awal cuti wajib diisi.',
            'end_date.required' => 'Tanggal akhir cuti wajib diisi.',
            'end_date.after_or_equal' => 'Tanggal akhir cuti harus sama atau setelah tanggal mulai.',
        ]);

        $resource->leaves()->create($validated);

        return back()->with('success', 'Jadwal cuti / ketidakhadiran berhasil dicatat.');
    }

    public function removeLeave(Project $project, Resource $resource, ResourceLeave $leave)
    {
        $this->authorize('update', $project);

        if ($leave->resource_id !== $resource->id || $resource->project_id !== $project->id) {
            abort(404);
        }

        $leave->delete();

        return back()->with('success', 'Jadwal cuti berhasil dihapus.');
    }
}
