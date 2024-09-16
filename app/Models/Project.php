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

   // Scope to filter tasks by status
   public function scopeTasksByStatus($query, $status)
   {
       return $query->where('status', $status);
   }

   // Scope to filter tasks by priority
   public function scopeTasksByPriority($query, $priority)
   {
       return $query->where('priority', $priority);
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
