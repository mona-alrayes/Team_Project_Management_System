<?php

namespace App\Services;

use Exception;
use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class TaskService
 * 
 * This service handles operations related to tasks, including fetching, storing, updating, and deleting tasks.
 */
class NoteService
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
    public function getNotesWithTasks(): array
    {
        try {


            $notes = Note::with(['task' , 'user'])
                ->paginate(5);

            // Throw a ModelNotFoundException if no tasks were found
            if ($notes->isEmpty()) {
                throw new ModelNotFoundException('No notes found.');
            }

            return [
                'data' => $notes->items(),
                'current_page' => $notes->currentPage(),
                'last_page' => $notes->lastPage(),
                'per_page' => $notes->perPage(),
                'total' => $notes->total(),
            ];
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve notes: ' . $e->getMessage());
        }
    }

    /**
     * Store a new task.
     * 
     * @param array $data
     * An associative array containing the task's details (e.g., title, description, priority, etc.).
     * 
     * @return Note
     * The created task resource.
     * 
     * @throws \Exception
     * Throws an exception if task creation fails.
     */
    public function storeNote(array $data): Note
    {
        try {
            $note = Note::create($data);

            if (!$note) {
                throw new Exception('Failed to create the note.');
            }

            return $note;
        } catch (Exception $e) {
            throw new Exception('Note creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a specific task by its ID.
     * 
     * @param int $id
     * The ID of the task to retrieve.
     * 
     * @return Note
     * The task resource.
     * 
     * @throws \Exception
     * Throws an exception if the task is not found.
     */
    public function showNotes(int $id): array
    {
        try {
            $task = Task::with(['user', 'notes'])->findOrFail($id);
            $userNotes = $task->user->notes;
            return [
                'task' => $task->title,
                'user' => $task->user,
                'user_notes' => $userNotes,
            ];
        } catch (ModelNotFoundException $e) {
            throw new Exception('Task not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve notes: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing task.

     * @param array $data
     * The data array containing the fields to update (e.g., title, status, priority).
     * @param string $id
     * The ID of the task to update.
     * 
     * @return Note
     * The updated task resource.
     * 
     * @throws \Exception
     * Throws an exception if the task is not found or update fails.
     */
    public function updateNote(array $data, string $id): Note
    {
        try {
            $note = Note::findOrFail($id);

            $note->update(array_filter($data));

            return $note;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Note not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update Note: ' . $e->getMessage());
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
    public function deleteNote(string $id): string
    {
        try {
            $note = Note::findOrFail($id);

            $note->delete();

            return "Note deleted successfully.";
        } catch (ModelNotFoundException $e) {
            throw new Exception('Note not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete Note: ' . $e->getMessage());
        }
    }

    public function restoreNote($id): array
    {
        try {
            $note = Note::withTrashed()->find($id);

            if (!$note) {
                throw new Exception('Note not found!');
            }
            if ($note && $note->trashed()) {
                $note->restore();
            }
            return [
                'status' => 'success',
                'message' => 'Note restored successfully',
                'note' => $note,
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
