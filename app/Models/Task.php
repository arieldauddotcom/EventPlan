<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'parent_id',
        'name',
        'description',
        'start_date',
        'end_date',
        'duration_days',
        'priority',
        'status',
        'progress_percentage',
        'sort_order',
        'early_start',
        'early_finish',
        'late_start',
        'late_finish',
        'total_float',
        'is_critical',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'duration_days' => 'integer',
        'progress_percentage' => 'integer',
        'sort_order' => 'integer',
        'early_start' => 'integer',
        'early_finish' => 'integer',
        'late_start' => 'integer',
        'late_finish' => 'integer',
        'total_float' => 'integer',
        'is_critical' => 'boolean',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function parent()
    {
        return $this->belongsTo(Task::class, 'parent_id');
    }

    public function subtasks()
    {
        return $this->hasMany(Task::class, 'parent_id')->orderBy('sort_order')->orderBy('start_date');
    }

    public function predecessorDependencies()
    {
        return $this->hasMany(TaskDependency::class, 'successor_id');
    }

    public function successorDependencies()
    {
        return $this->hasMany(TaskDependency::class, 'predecessor_id');
    }

    public function predecessors()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'successor_id', 'predecessor_id')
                    ->withPivot('id', 'type', 'lag_days')
                    ->withTimestamps();
    }

    public function successors()
    {
        return $this->belongsToMany(Task::class, 'task_dependencies', 'predecessor_id', 'successor_id')
                    ->withPivot('id', 'type', 'lag_days')
                    ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(TaskAssignment::class);
    }

    public function resources()
    {
        return $this->belongsToMany(Resource::class, 'task_assignments')
                    ->withPivot('allocation_percentage')
                    ->withTimestamps();
    }
}
