<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectMember extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'role', // 'leader' or 'member'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isLeader(): bool
    {
        return in_array($this->role, ['leader', 'co-leader']);
    }

    public function isMember(): bool
    {
        return $this->role === 'member';
    }

    public function isOwner(): bool
    {
        return $this->project && $this->user_id === $this->project->owner_id;
    }

    public function isCoLeader(): bool
    {
        return $this->isLeader() && !$this->isOwner();
    }

    public function getRoleLabelAttribute(): string
    {
        if ($this->isOwner()) {
            return 'Leader (Owner)';
        }
        if ($this->isLeader()) {
            return 'Co-Leader';
        }
        return 'Member';
    }
}
