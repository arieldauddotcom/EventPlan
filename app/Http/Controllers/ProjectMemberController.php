<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectMemberController extends Controller
{
    /**
     * Keluarkan anggota dari proyek oleh Project Leader.
     */
    public function destroy(Project $project, ProjectMember $member)
    {
        $user = Auth::user();

        if (!$project->isLeader($user)) {
            abort(403, 'Hanya Project Leader yang dapat mengeluarkan anggota proyek.');
        }

        if ($member->project_id !== $project->id) {
            abort(404);
        }

        // Leader / Owner tidak dapat dikeluarkan oleh dirinya sendiri melalui endpoint ini
        if ($member->user_id === $project->owner_id || $member->isLeader()) {
            return back()->with('error', 'Project Leader tidak dapat dikeluarkan dari proyek.');
        }

        $memberName = $member->user->name ?? 'Anggota';
        $member->delete();

        return back()->with('success', $memberName . ' telah dikeluarkan dari proyek.');
    }
}
