<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'work_calendar_id',
        'name',
        'organization',
        'description',
        'start_date',
        'deadline',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function workCalendar()
    {
        return $this->belongsTo(WorkCalendar::class);
    }

    public function members()
    {
        return $this->hasMany(ProjectMember::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'project_members')->withPivot('role')->withTimestamps();
    }

    public function invitations()
    {
        return $this->hasMany(ProjectInvitation::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('sort_order')->orderBy('start_date');
    }

    public function resources()
    {
        return $this->hasMany(Resource::class);
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->owner_id === $user->id;
    }

    public function isLeader(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->owner_id === $user->id) {
            return true;
        }

        return $this->members()
            ->where('user_id', $user->id)
            ->whereIn('role', ['leader', 'co-leader'])
            ->exists();
    }

    public function isMember(User $user): bool
    {
        return $this->members()
            ->where('user_id', $user->id)
            ->where('role', 'member')
            ->exists();
    }

    public function hasAccess(User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($this->isOwnedBy($user)) {
            return true;
        }

        return $this->members()->where('user_id', $user->id)->exists();
    }

    public function getUserRole(User $user): ?string
    {
        if ($this->isLeader($user)) {
            return 'leader';
        }

        $member = $this->members()->where('user_id', $user->id)->first();
        return $member ? $member->role : null;
    }
}
