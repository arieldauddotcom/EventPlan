<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskDependency extends Model
{
    use HasFactory;

    protected $fillable = [
        'predecessor_id',
        'successor_id',
        'type',
        'lag_days',
    ];

    protected $casts = [
        'lag_days' => 'integer',
    ];

    public function predecessor()
    {
        return $this->belongsTo(Task::class, 'predecessor_id');
    }

    public function successor()
    {
        return $this->belongsTo(Task::class, 'successor_id');
    }
}
