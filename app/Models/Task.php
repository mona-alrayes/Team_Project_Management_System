<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Task Model
 *
 * Represents a task assigned to a user with fields like title, description,
 * priority, status, assigned user, and due date. The model also handles
 * date formatting using accessors and mutators.
 */
class Task extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'priority',
        'assigned_to',
        'status',
        'due_date',
        'project_id',
        'status_changed_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'datetime',
        'status_changed_at' => 'datetime'
    ];

    /**
     * Mutator for setting the 'due_date' attribute.
     *
     * This mutator ensures that the input date is parsed and saved
     * in the 'Y-m-d H:i:s' format (e.g., "2024-09-20 14:30:00") in the database.
     *
     * @param string $dueDate The input due date in any Carbon-parsable format.
     * @return void
     */
    public function setDueDateAttribute($dueDate)
    {
        $this->attributes['due_date'] = Carbon::parse($dueDate)->format('Y-m-d H:i:s');
    }

    /**
     * Accessor for retrieving the 'due_date' attribute.
     *
     * This accessor formats the due date into a more human-readable form like
     * "Saturday, September 2024 at 02:30 PM" when accessed.
     *
     * @param string $value The stored due date value from the database.
     * @return string The formatted date.
     */
    public function getDueDateAttribute($value)
    {
        return Carbon::parse($value)->format('l, F Y \a\t h:i A');
    }

    /**
     * Scope for filtering tasks by priority.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $priority The priority to filter by (e.g., "high", "medium").
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope for filtering tasks by status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $status The status to filter by (e.g., "To Do", "In Progress").
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for sorting tasks by due date.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $sortOrder The sorting order (e.g., "asc" or "desc").
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSortByDueDate($query, $sortOrder = 'asc')
    {
        return $query->orderBy('due_date', $sortOrder);
    }

    /**
     * Relationship with the User model.
     *
     * Defines the relationship between the task and the user it is assigned to.
     * Each task belongs to a single user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'user_id');
    }
}