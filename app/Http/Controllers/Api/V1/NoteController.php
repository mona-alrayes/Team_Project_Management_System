<?php

namespace App\Http\Controllers\Api\V1;


use Illuminate\Http\Request;
use App\Services\NoteService;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNoteRequest;
use App\Http\Requests\UpdateNoteRequest;
use App\Http\Resources\NoteResource;


class NoteController extends Controller
{
    protected $NoteService;

    /**
     * Constructor for TaskController
     *
     * @param NoteService $NoteService The task service for handling task-related logic.
     */
    public function __construct(NoteService $NoteService)
    {
        $this->NoteService = $NoteService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index() // returns notes with the task they belong to
    {
        $notes = $this->NoteService->getNotesWithTasks();

        return response()->json([
            'status' => 'success',
            'message' => 'Notes retrieved successfully',
            'Notes' => [
                'info' => NoteResource::collection($notes['data']),
                'current_page' => $notes['current_page'],
                'last_page' => $notes['last_page'],
                'per_page' => $notes['per_page'],
                'total' => $notes['total'],
            ],
        ], 200); // OK
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNoteRequest $request ) 
    {
        $note = $this->NoteService->storeNote($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Note created successfully',
            'note' => NoteResource::make($note),
        ], 201); // Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $task_id) // return notes on specific task id
    {
        $fetchedData = $this->NoteService->showNotes($task_id);

        return response()->json([
            'status' => 'success',
            'message' => 'Notes retrieved successfully',
            'task' => $fetchedData['task'],
            'user' => $fetchedData['user'],
            'user_notes'=> $fetchedData['user_notes'],
        ], 200); // OK
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNoteRequest $request, string $id ) // update note on specific task 
    {
        $note = $this->NoteService->updateNote($request->validated(), $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Note updated successfully',
            'task' => NoteResource::make($note),
        ], 200); // OK 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id ) //delete note
    {
        $message = $this->NoteService->deleteNote($id);

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], 200); // OK
    }

    public function restore(string $id) //restore softdeleted note
    {
        $message= $this->NoteService->restoreNote($id);
        return response()->json([
            'status' => $message['status'],
            'message' => $message['message'],
            'note' => new NoteResource($message['note']),
        ], 200); // OK
    }
}
