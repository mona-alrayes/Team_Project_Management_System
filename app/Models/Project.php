<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
    // Define many-to-many relationship with User 
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'contribution_hours', 'last_activity');
    }

    // Define the most recent task
    public function lastTask()
    {
        return $this->hasOne(Task::class)->latestOfMany();
    }

    // Define the oldest task
    public function oldestTask()
    {
        return $this->hasOne(Task::class)->oldestOfMany();
    }

    public function userTasks()
    {
        // Assumes User is related to Project via a pivot table and Task is related to Project
        return $this->hasManyThrough(Task::class, Project::class, 'user_id', 'project_id', 'id', 'id')
            ->where('tasks.assigned_to', $this->id); // Optional: Filter tasks assigned to the user
    }

    // Scope to filter tasks by status
    public function scopeTasksByStatus($query, $status)
    {
        return $query->whereRelation('userTasks', 'status', $status);
    }

    // Scope to filter tasks by priority
    public function scopeTasksByPriority($query, $priority)
    {
        return $query->whereRelation('userTasks', 'priority', $priority);
    }
}
