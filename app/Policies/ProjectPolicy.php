<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * Member, Leader, dan Admin boleh melihat.
     */
    public function view(User $user, Project $project): bool
    {
        return $project->hasAccess($user);
    }

    /**
     * Determine whether the user can create models.
     * Semua user dengan global role 'user' atau 'admin' boleh membuat proyek.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model (pengaturan proyek).
     * Hanya Project Leader yang boleh.
     */
    public function update(User $user, Project $project): bool
    {
        return $project->isLeader($user);
    }

    /**
     * Determine whether the user can delete the model.
     * Hanya Project Leader / Owner yang boleh.
     */
    public function delete(User $user, Project $project): bool
    {
        return $project->isLeader($user);
    }

    /**
     * Determine whether the user can manage settings, calendar, dependencies, resources, and assignments.
     * Hanya Project Leader yang boleh.
     */
    public function manage(User $user, Project $project): bool
    {
        return $project->isLeader($user);
    }

    /**
     * Determine whether the user can invite or manage members.
     * Hanya Project Leader yang boleh.
     */
    public function manageMembers(User $user, Project $project): bool
    {
        return $project->isLeader($user);
    }
}
