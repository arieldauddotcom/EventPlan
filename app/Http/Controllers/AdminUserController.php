<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    /**
     * Tampilkan daftar seluruh pengguna.
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $role = $request->query('role');

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($role, function ($query, $role) {
                $query->where('role', $role);
            })
            ->withCount(['ownedProjects', 'projects'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'regular_users' => User::where('role', 'user')->count(),
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'planning_projects' => Project::where('status', 'planning')->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'draft_projects' => Project::where('status', 'draft')->count(),
            'total_tasks' => Task::count(),
        ];

        return view('admin.users.index', compact('users', 'search', 'role', 'stats'));
    }

    /**
     * Tampilkan seluruh proyek secara global untuk administrator.
     */
    public function projects(Request $request)
    {
        $search = $request->query('search');
        $status = $request->query('status');
        $ownerId = $request->query('owner_id');

        $projectsQuery = Project::query()
            ->with(['owner', 'workCalendar'])
            ->withCount(['tasks', 'members'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('organization', 'like', "%{$search}%")
                      ->orWhereHas('owner', function ($oq) use ($search) {
                          $oq->where('name', 'like', "%{$search}%")
                             ->orWhere('username', 'like', "%{$search}%")
                             ->orWhere('email', 'like', "%{$search}%");
                      });
                });
            })
            ->when($status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->when($ownerId, function ($query, $ownerId) {
                $query->where('owner_id', $ownerId);
            });

        $projects = (clone $projectsQuery)->latest()->paginate(12)->withQueryString();

        $stats = [
            'total_projects' => Project::count(),
            'active_projects' => Project::where('status', 'active')->count(),
            'planning_projects' => Project::where('status', 'planning')->count(),
            'completed_projects' => Project::where('status', 'completed')->count(),
            'draft_projects' => Project::where('status', 'draft')->count(),
            'total_tasks' => Task::count(),
        ];

        $selectedOwner = $ownerId ? User::find($ownerId) : null;
        $usersList = User::has('ownedProjects')->orderBy('name')->get(['id', 'name', 'username']);

        return view('admin.projects.index', compact('projects', 'stats', 'search', 'status', 'ownerId', 'selectedOwner', 'usersList'));
    }

    /**
     * Hapus proyek oleh administrator.
     */
    public function destroyProject(Project $project)
    {
        $projectName = $project->name;
        $project->delete();

        return back()->with('success', "Proyek '{$projectName}' berhasil dihapus oleh Administrator.");
    }

    /**
     * Form edit data pengguna oleh admin.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Perbarui data pengguna oleh admin.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => [
                'required',
                'string',
                'max:50',
                'alpha_dash',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'role' => ['required', 'in:admin,user'],
            'new_password' => ['nullable', Password::min(6)],
        ], [
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, strip, dan underscore.',
            'username.unique' => 'Username telah digunakan.',
            'email.unique' => 'Email telah digunakan.',
            'new_password.min' => 'Kata sandi baru minimal :min karakter.',
        ]);

        $user->name = $validated['name'];
        $user->username = strtolower($validated['username']);
        $user->email = strtolower($validated['email']);
        $user->role = $validated['role'];

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('success', "Pengguna {$user->name} berhasil diperbarui.");
    }

    /**
     * Hapus pengguna.
     */
    public function destroy(User $user)
    {
        // Mencegah admin menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')->with('success', "Pengguna {$name} berhasil dihapus.");
    }
}
