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
        'status_changed_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'due_date' => 'datetime',
        'status_changed_at' => 'datetime',
    ];

    /**
     * Accessor for retrieving the 'due_date' attribute.
     *
     * @param string $value The stored due date value from the database.
     * @return string The formatted date.
     */
    public function getDueDateAttribute($value)
    {
        return Carbon::parse($value)->format('l, F Y \a\t h:i A');
    }

    /**
     * Mutator for setting the 'due_date' attribute.
     *
     * @param string $dueDate The input due date in any Carbon-parsable format.
     * @return void
     */
    public function setDueDateAttribute($dueDate)
    {
        $this->attributes['due_date'] = Carbon::parse($dueDate)->format('Y-m-d H:i:s');
    }

    // Relationships

    /**
     * Relationship with the User model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    /**
     * Relationship with the Project model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * Relationship with the Note model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    // Scopes

    /**
     * Scope to filter tasks by priority.
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
     * Scope to filter tasks by status.
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
     * Scope to sort tasks by due date.
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
     * Scope to order tasks by creation date in ascending order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOldest($query)
    {
        return $query->orderBy('created_at', 'asc');
    }

    /**
     * Scope to order tasks by creation date in descending order.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}

