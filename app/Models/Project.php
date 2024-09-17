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

    // Define many-to-many relationship with User 
    public function users()
    {
        return $this->belongsToMany(User::class)->withPivot('role', 'contribution_hours', 'last_activity');
    }
   // returns the lastest task 
    public function newestTask()
    {
        return $this->hasOne(Task::class)->latestOfMany();
    }

    // returns the oldest task
    public function oldestTask()
    {
        return $this->hasOne(Task::class)->oldestOfMany();
    }
 // returns task that has the highest priority that has title condition using  hasOne --- ofMany
    public function highPriorityWithTitle($title)
    {
        return $this->hasOne(Task::class)
            ->ofMany([], function ($query) use ($title) {
                $query->where('priority', 'high')
                    ->where('title', 'LIKE', '%' . $title . '%');
            });
    }
}
