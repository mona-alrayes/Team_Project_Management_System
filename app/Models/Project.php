<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    // Define one-to-many relationship with Task
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    // Define many-to-many relationship with User 
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'contribution_hours', 'last_activity');
    }
#TODO go back here there is alot of logic and work to todo
    // Define the most recent task
    public function newestTask()
    {
        return $this->hasOne(Task::class)->latestOfMany();
    }

    // Define the oldest task
    public function oldestTask()
    {
        return $this->hasOne(Task::class)->oldestOfMany();
    }

    // Scope to filter tasks by status
    public function TasksByStatus($status)
    {
        return $this->tasks()->where('status', $status)->get();
    }

    // Scope to filter tasks by priority
    public function TasksByPriority($priority)
    {
        return $this->tasks()->where('priority', $priority)->get();
    }

    public function highPriorityWithTitle($title)
    {
        return $this->hasOne(Task::class)
            ->ofMany([], function ($query) use ($title) {
                $query->where('priority', 'high')
                    ->where('title', 'LIKE', '%' . $title . '%');
            });
    }
}
