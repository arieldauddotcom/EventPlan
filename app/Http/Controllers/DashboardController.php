<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $projectsQuery = Project::query()->where(function ($q) use ($user) {
            $q->where('owner_id', $user->id)
              ->orWhereHas('members', fn($mq) => $mq->where('user_id', $user->id));
        });

        $projectIds = (clone $projectsQuery)->pluck('id');

        $totalProjects = $projectIds->count();
        $activeProjects = (clone $projectsQuery)->where('status', 'active')->count();
        $completedProjects = (clone $projectsQuery)->where('status', 'completed')->count();
        $planningProjects = (clone $projectsQuery)->where('status', 'planning')->count();

        // Statistik Tugas di seluruh proyek pengguna
        $totalTasks = Task::whereIn('project_id', $projectIds)->count();
        $completedTasks = Task::whereIn('project_id', $projectIds)->where('status', 'completed')->count();
        $inProgressTasks = Task::whereIn('project_id', $projectIds)->where('status', 'in_progress')->count();
        $criticalTasksCount = Task::whereIn('project_id', $projectIds)->where('is_critical', true)->count();

        // Deadline terdekat (7 hari ke depan)
        $today = Carbon::today();
        $upcomingDeadlines = Task::with(['project', 'assignments.resource'])
            ->whereIn('project_id', $projectIds)
            ->where('status', '!=', 'completed')
            ->whereBetween('end_date', [$today, $today->copy()->addDays(7)])
            ->orderBy('end_date')
            ->take(5)
            ->get();

        $recentProjects = (clone $projectsQuery)
            ->with(['owner', 'workCalendar', 'tasks'])
            ->latest()
            ->take(5)
            ->get();

        // Ringkasan Global Khusus Administrator
        $adminStats = null;
        if ($user->isAdmin()) {
            $adminStats = [
                'total_users' => User::count(),
                'admin_users' => User::where('role', 'admin')->count(),
                'regular_users' => User::where('role', 'user')->count(),
                'global_projects' => Project::count(),
                'global_active_projects' => Project::where('status', 'active')->count(),
                'global_completed_projects' => Project::where('status', 'completed')->count(),
            ];
        }

        return view('dashboard', compact(
            'totalProjects',
            'activeProjects',
            'completedProjects',
            'planningProjects',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'criticalTasksCount',
            'upcomingDeadlines',
            'recentProjects',
            'adminStats'
        ));
    }
}
