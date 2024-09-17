<?php

namespace App\Services;

use Exception;
use App\Models\Note;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NoteService
{
    /**
     * Retrieve all notes with their associated tasks and users.
     *
     * @return array
     * @throws Exception
     */
    public function getNotesWithTasks(): array
    {
        try {
            $notes = Note::with(['task', 'user'])->paginate(5);

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
     * Store a new note.
     *
     * @param array $data
     * @return Note
     * @throws Exception
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
     * Retrieve notes for a specific task by its ID.
     *
     * @param int $id
     * @return array
     * @throws Exception
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
     * Update an existing note.
     *
     * @param array $data
     * @param string $id
     * @return Note
     * @throws Exception
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
            throw new Exception('Failed to update note: ' . $e->getMessage());
        }
    }

    /**
     * Delete a note by its ID.
     *
     * @param string $id
     * @return string
     * @throws Exception
     */
    public function deleteNote(string $id): string
    {
        try {
            $note = Note::findOrFail($id);

            $note->delete();

            return 'Note deleted successfully.';
        } catch (ModelNotFoundException $e) {
            throw new Exception('Note not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete note: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted note.
     *
     * @param string $id
     * @return array
     * @throws Exception
     */
    public function restoreNote(string $id): array
    {
        try {
            $note = Note::withTrashed()->find($id);

            if (!$note) {
                throw new Exception('Note not found.');
            }

            if ($note->trashed()) {
                $note->restore();
            }

            return [
                'status' => 'success',
                'message' => 'Note restored successfully',
                'note' => $note,
            ];
        } catch (Exception $e) {
            return [
                'status' => 'error',
                'message' => 'An error occurred during restoration.',
                'errors' => $e->getMessage(),
            ];
        }
    }
}
