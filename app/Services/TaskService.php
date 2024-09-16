<?php

namespace App\Services;

use Exception;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class TaskService
 * 
 * This service handles operations related to tasks, including fetching, storing, updating, and deleting tasks.
 */
class TaskService
{
    /**
     * Retrieve all tasks with optional filters and sorting.
     * 
     * @param Request $request
     * The request object containing optional filters (priority, status) and sorting options (sort_order).
     * 
     * @return array
     * An array containing paginated task resources.
     * 
     * @throws \Exception
     * Throws an exception if there is an error during the process.
     */
    public function getAllTasks(Request $request): array
    {
        try {

            // Check if oldest_task or newest_task is set
            if ($request->has('oldest_task')) {
                $oldestTask = Task::oldestTask();
                if (!$oldestTask) {
                    throw new ModelNotFoundException('No tasks found.');
                }
                return [
                    'data' => [$oldestTask],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 1,
                    'total' => 1,
                ];
            }

            if ($request->has('newest_task')) {
                $newestTask = Task::newestTask();
                if (!$newestTask) {
                    throw new ModelNotFoundException('No tasks found.');
                }
                return [
                    'data' => [$newestTask],
                    'current_page' => 1,
                    'last_page' => 1,
                    'per_page' => 1,
                    'total' => 1,
                ];
            }
            // If no specific oldest/newest task is requested, proceed with regular query and pagination
            $tasks = Task::with(['user', 'project'])
                ->when($request->priority, fn($q) => $q->priority($request->priority))
                ->when($request->status, fn($q) => $q->status($request->status))
                ->when($request->sort_order, fn($q) => $q->sortByDueDate($request->sort_order))
                ->paginate(5);

            // Throw a ModelNotFoundException if no tasks were found
            if ($tasks->isEmpty()) {
                throw new ModelNotFoundException('No tasks found.');
            }

            return [
                'data' => $tasks->items(), // The items on the current page
                'current_page' => $tasks->currentPage(),
                'last_page' => $tasks->lastPage(),
                'per_page' => $tasks->perPage(),
                'total' => $tasks->total(),
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve tasks: ' . $e->getMessage());
        }
    }

    /**
     * Store a new task.
     * 
     * @param array $data
     * An associative array containing the task's details (e.g., title, description, priority, etc.).
     * 
     * @return Task
     * The created task resource.
     * 
     * @throws \Exception
     * Throws an exception if task creation fails.
     */
    public function storeTask(array $data): Task
    {
        try {
            $task = Task::create($data);

            if (!$task) {
                throw new Exception('Failed to create the task.');
            }

            return $task;
        } catch (Exception $e) {
            throw new Exception('Task creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a specific task by its ID.
     * 
     * @param int $id
     * The ID of the task to retrieve.
     * 
     * @return Task
     * The task resource.
     * 
     * @throws \Exception
     * Throws an exception if the task is not found.
     */
    public function showTask(int $id): Task
    {
        try {
            $task = Task::findOrFail($id);
            return $task;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Task not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve task: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing task.
     * 
     * @param array $data
     * The data array containing the fields to update (e.g., title, status, priority).
     * @param string $id
     * The ID of the task to update.
     * 
     * @return Task
     * The updated task resource.
     * 
     * @throws \Exception
     * Throws an exception if the task is not found or update fails.
     */
    public function updateTask(array $data, string $id): Task
    {
        try {
            $userId = Auth::id(); // Get the ID of the authenticated user
            $user = User::findOrFail($userId); // Find the authenticated user
            $task = Task::findOrFail($id);
            $task->update(array_filter($data));
            // Get the project associated with the task
            $project = $task->project;
            // Check if the user is associated with the project
            if ($user->projects->contains($project)) {
                // Retrieve the existing distribution_hours value
                $pivotData = $user->projects()->where('project_id', $project->id)->first()->pivot;

                // Determine the new distribution_hours value
                $newDistributionHours = $pivotData->distribution_hours === null
                    ? 0
                    : $pivotData->distribution_hours + 2;

                // Update the pivot table
                $user->projects()->updateExistingPivot($project->id, [
                    'last_activity' => now(),
                    'distribution_hours' => $newDistributionHours,
                ]);
            } else {
                // Attach a new record to the pivot table if it doesn't exist
                $user->projects()->attach($project->id, [
                    'last_activity' => now(),
                    'distribution_hours' => null,
                ]);
            }

            return $task;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Task not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * Update the status of a specific task.
     * 
     * @param array $data
     * The data array containing the new status field.
     * @param string $id
     * The ID of the task to update.
     * 
     * @return Task
     * The updated task resource.
     * 
     * @throws \Exception
     * Throws an exception if the task is not found or update fails.
     */
    public function updateStatus(array $data, string $id): Task
    {
        try {
            $userId = Auth::id(); // Get the ID of the authenticated user
            $user = User::findOrFail($userId); // Find the authenticated user
            $task = Task::findOrFail($id); // Find the task

            // Update the task status and other fields
            $task->status_changed_at = now();
            $task->update(array_filter($data));

            // Get the project associated with the task
            $project = $task->project;

            // Check if the user is associated with the project
            if ($user->projects->contains($project)) {
                // Retrieve the existing distribution_hours value
                $pivotData = $user->projects()->where('project_id', $project->id)->first()->pivot;

                // Determine the new distribution_hours value
                $newDistributionHours = $pivotData->distribution_hours === null
                    ? 0
                    : $pivotData->distribution_hours + 12;   // assuming developer tasks 12 hours for everychange

                // Update the pivot table
                $user->projects()->updateExistingPivot($project->id, [
                    'last_activity' => now(),
                    'distribution_hours' => $newDistributionHours,
                ]);
            } else {
                // Attach a new record to the pivot table if it doesn't exist
                $user->projects()->attach($project->id, [
                    'last_activity' => now(),
                    'distribution_hours' => null,
                ]);
            }

            return $task;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Task not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update task: ' . $e->getMessage());
        }
    }

    /**
     * Delete a task by its ID.
     * 
     * @param string $id
     * The ID of the task to delete.
     * 
     * @return string
     * A message confirming the successful deletion.
     * 
     * @throws \Exception
     * Throws an exception if the task is not found or deletion fails.
     */
    public function deleteTask(string $id): string
    {
        try {
            $task = Task::findOrFail($id);

            $task->delete();

            return "Task deleted successfully.";
        } catch (ModelNotFoundException $e) {
            throw new Exception('Task not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete task: ' . $e->getMessage());
        }
    }

    public function restoreTask($id): array
    {
        try {
            $task = Task::withTrashed()->find($id);

            if (!$task) {
                throw new Exception('Task not found!');
            }
            if ($task && $task->trashed()) {
                $task->restore();
            }
            return [
                'status' => 'success',
                'message' => 'Task restored successfully',
                'task' => $task,
            ];
        } catch (Exception $e) {
            // Handle any other exceptions
            return [
                'status' => 'error',
                'message' => 'An error occurred during deletion.',
                'errors' => $e->getMessage(),
            ];
        }
    }
}
