<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\WorkCalendar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Project::query()
            ->where(function ($q) use ($user) {
                if ($user->isAdmin()) {
                    return; // Admin bisa melihat semua
                }
                $q->where('owner_id', $user->id)
                  ->orWhereHas('members', fn($mq) => $mq->where('user_id', $user->id));
            })
            ->with(['owner', 'workCalendar', 'members']);

        // Filter berdasarkan peran di proyek: dipimpin (leader) atau diikuti (member)
        if ($request->filled('role')) {
            if ($request->role === 'leader') {
                $query->where(function ($q) use ($user) {
                    $q->where('owner_id', $user->id)
                      ->orWhereHas('members', fn($mq) => $mq->where('user_id', $user->id)->whereIn('role', ['leader', 'co-leader']));
                });
            } elseif ($request->role === 'member') {
                $query->whereHas('members', fn($mq) => $mq->where('user_id', $user->id)->where('role', 'member'))
                      ->where('owner_id', '!=', $user->id);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('owner_id')) {
            $query->where('owner_id', $request->owner_id);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('organization', 'like', '%' . $request->search . '%');
            });
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(9);

        return view('projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $calendars = WorkCalendar::all();
        return view('projects.create', compact('calendars'));
    }

    /**
     * Store a newly created resource in storage.
     * Aturan 3 & 4: Semua pengguna role 'user' dapat membuat proyek, otomatis menjadi 'leader'.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:draft,planning,active,completed,archived',
            'work_calendar_id' => 'nullable|exists:work_calendars,id',
            'calendar_working_days' => 'nullable|array',
            'calendar_working_days.*' => 'integer|between:1,7',
        ], [
            'name.required' => 'Nama proyek wajib diisi.',
            'start_date.required' => 'Tanggal mulai proyek wajib ditentukan.',
            'deadline.required' => 'Deadline proyek wajib ditentukan.',
            'deadline.after_or_equal' => 'Deadline tidak boleh lebih awal dari tanggal mulai.',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $user = Auth::user();

            $calendarId = $validated['work_calendar_id'] ?? null;
            if (!$calendarId) {
                $workingDays = $request->input('calendar_working_days', [1, 2, 3, 4, 5]);
                $calendar = WorkCalendar::create([
                    'name' => 'Kalender - ' . $validated['name'],
                    'working_days' => array_map('intval', $workingDays),
                    'hours_per_day' => 8.00,
                ]);
                $calendarId = $calendar->id;
            }

            $project = Project::create([
                'owner_id' => $user->id,
                'work_calendar_id' => $calendarId,
                'name' => $validated['name'],
                'organization' => $validated['organization'] ?? null,
                'description' => $validated['description'] ?? null,
                'start_date' => $validated['start_date'],
                'deadline' => $validated['deadline'],
                'status' => $validated['status'],
            ]);

            // Aturan 4: Pembuat otomatis menjadi 'leader' pada tabel project_members
            ProjectMember::create([
                'project_id' => $project->id,
                'user_id' => $user->id,
                'role' => 'leader',
            ]);

            return redirect()->route('projects.show', $project)
                ->with('success', 'Proyek berhasil dibuat. Anda ditugaskan sebagai Project Leader.');
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        $this->authorize('view', $project);

        $project->load([
            'owner',
            'workCalendar.holidays',
            'members.user',
            'invitations.invitee',
            'tasks.predecessors',
            'tasks.successors',
            'tasks.resources',
            'tasks.assignments.resource',
            'resources.leaves',
            'resources.assignments',
        ]);

        return view('projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        $this->authorize('update', $project);

        $calendars = WorkCalendar::all();
        return view('projects.edit', compact('project', 'calendars'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'organization' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'deadline' => 'required|date|after_or_equal:start_date',
            'status' => 'required|in:draft,planning,active,completed,archived',
            'work_calendar_id' => 'required|exists:work_calendars,id',
        ], [
            'name.required' => 'Nama proyek wajib diisi.',
            'deadline.after_or_equal' => 'Deadline tidak boleh lebih awal dari tanggal mulai.',
        ]);

        $project->update($validated);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Informasi proyek berhasil diperbarui.');
    }

    /**
     * Perbarui status proyek secara cepat langsung dari halaman detail proyek.
     */
    public function updateStatus(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'status' => 'required|in:draft,planning,active,completed,archived',
        ], [
            'status.required' => 'Status proyek wajib dipilih.',
            'status.in' => 'Status proyek tidak valid.',
        ]);

        $oldStatus = $project->status;
        $newStatus = $validated['status'];

        if ($oldStatus === $newStatus) {
            return back()->with('info', 'Status proyek tidak berubah.');
        }

        $project->update(['status' => $newStatus]);

        $statusLabels = [
            'draft' => 'Draft',
            'planning' => 'Perencanaan (Planning)',
            'active' => 'Aktif (Active)',
            'completed' => 'Selesai (Completed)',
            'archived' => 'Diarsipkan (Archived)',
        ];

        $label = $statusLabels[$newStatus] ?? ucfirst($newStatus);

        return back()->with('success', "Status proyek '{$project->name}' berhasil diubah menjadi '{$label}'!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }

    /**
     * Tambah hari libur kalender kerja proyek
     */
    public function addHoliday(Request $request, Project $project)
    {
        $this->authorize('manage', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
        ]);

        $project->workCalendar->holidays()->firstOrCreate(
            ['date' => $validated['date']],
            ['name' => $validated['name']]
        );

        return back()->with('success', 'Hari libur berhasil ditambahkan.');
    }

    /**
     * Hapus hari libur kalender kerja proyek
     */
    public function removeHoliday(Project $project, $holidayId)
    {
        $this->authorize('manage', $project);

        $project->workCalendar->holidays()->where('id', $holidayId)->delete();

        return back()->with('success', 'Hari libur berhasil dihapus.');
    }
}
