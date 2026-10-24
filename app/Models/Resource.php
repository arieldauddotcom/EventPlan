<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'user_id',
        'name',
        'email',
        'phone',
        'role',
        'cost_per_hour',
        'max_hours_per_day',
    ];

    protected $casts = [
        'cost_per_hour' => 'decimal:2',
        'max_hours_per_day' => 'decimal:1',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaves()
    {
        return $this->hasMany(ResourceLeave::class);
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignments')
                    ->withPivot('allocation_percentage')
                    ->withTimestamps();
    }
}
