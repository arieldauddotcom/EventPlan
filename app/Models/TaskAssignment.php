<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'resource_id',
        'allocation_percentage',
    ];

    protected $casts = [
        'allocation_percentage' => 'integer',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function resource()
    {
        return $this->belongsTo(Resource::class);
    }
}
