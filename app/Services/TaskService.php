<?php

namespace App\Services;

use Exception;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $tasks = Task::with(['user', 'project'])
                ->when($request->priority, fn($q) => $q->priority($request->priority))
                ->when($request->status, fn($q) => $q->status($request->status))
                ->when($request->sort_order, fn($q) => $q->sortByDueDate($request->sort_order))
                ->paginate(5);

            if ($tasks->isEmpty()) {
                throw new ModelNotFoundException('No tasks found.');
            }

            return [
                'data' => $tasks->items(),
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
            $this->updatePivotForTask($task, 2); // Update pivot for manager (2 hours)

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
            return Task::findOrFail($id);
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
            $task = Task::findOrFail($id);
            $task->update(array_filter($data));

            $this->updatePivotForTask($task, 2); // Update pivot for manager (2 hours)

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
            $task = Task::findOrFail($id);
            $task->status_changed_at = now();
            $task->update(array_filter($data));

            $this->updatePivotForTask($task, 12); // Update pivot for developer (12 hours)

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

    /**
     * Restore a soft-deleted task.
     * 
     * @param string $id
     * The ID of the task to restore.
     * 
     * @return array
     * The response message along with the restored task details.
     * 
     * @throws \Exception
     * Throws an exception if the task is not found.
     */
    public function restoreTask(string $id): array
    {
        try {
            $task = Task::withTrashed()->find($id);

            if (!$task) {
                throw new Exception('Task not found!');
            }

            if ($task->trashed()) {
                $task->restore();
            }

            return [
                'status' => 'success',
                'message' => 'Task restored successfully',
                'task' => $task,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'An error occurred during restoration.',
                'errors' => $e->getMessage(),
            ];
        }
    }

    /**
     * Helper method to update pivot table for the task's associated project and user.
     * 
     * @param Task $task
     * The task whose project and user's pivot data should be updated.
     * @param int $hours
     * The number of hours to add to the `distribution_hours`.
     * 
     * @return void
     * 
     * @throws \Exception
     * Throws an exception if the user is not associated with the project.
     */
    private function updatePivotForTask(Task $task, int $hours): void
    {
        $userId = Auth::id(); // Get the ID of the authenticated user
        $user = User::findOrFail($userId); // Find the authenticated user
        $project = $task->project; // Get the project associated with the task

        // Check if the user is associated with the project
        if ($project) {
            $pivot = $user->projects()->find($project->id)->pivot;

            // Update pivot fields
            $pivot->increment('distribution_hours', $hours);
            $pivot->update(['last_activity' => now()]);
        } else {
            throw new Exception('User is not associated with the project.');
        }
    }
}
