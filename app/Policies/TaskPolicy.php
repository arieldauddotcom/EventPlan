<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;

class TaskPolicy
{
    /**
     * Leader boleh membuat tugas baru.
     */
    public function create(User $user, Project $project): bool
    {
        return $project->isLeader($user);
    }

    /**
     * Leader boleh update seluruh data tugas (nama, durasi, tanggal, prioritas, dependensi).
     * Member hanya boleh memperbarui progress/status tugas jika tugas tersebut di-assign padanya.
     */
    public function update(User $user, Task $task): bool
    {
        $project = $task->project;

        if ($project->isLeader($user)) {
            return true;
        }

        // Jika dia member proyek, cek apakah ada resource assignment atas email/user ini
        if ($project->isMember($user)) {
            return $task->assignments()->whereHas('resource', function ($query) use ($user) {
                $query->where('email', $user->email);
            })->exists();
        }

        return false;
    }

    /**
     * Hanya Leader yang boleh menghapus tugas.
     */
    public function delete(User $user, Task $task): bool
    {
        return $task->project->isLeader($user);
    }
}
