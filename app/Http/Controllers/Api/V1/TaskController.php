<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\TaskService;
use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateStatusRequest;

/**
 * TaskController
 *
 * This controller handles the CRUD operations for tasks, including task creation,
 * retrieval, updating, and deletion. It also manages updating task status by assigned users.
 */
class TaskController extends Controller
{
    protected $TaskService;

    /**
     * Constructor for TaskController
     *
     * @param TaskService $TaskService The task service for handling task-related logic.
     */
    public function __construct(TaskService $TaskService)
    {
        $this->TaskService = $TaskService;
    }

    /**
     * Display a listing of tasks.
     *
     * @param Request $request HTTP request object containing any filter parameters.
     * @return \Illuminate\Http\JsonResponse JSON response containing task data and pagination details.
     */
    public function index(Request $request)
    {
        $tasks = $this->TaskService->getAllTasks($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Tasks retrieved successfully',
            'users' => [
                'info' => TaskResource::collection($tasks['data']),
                'current_page' => $tasks['current_page'],
                'last_page' => $tasks['last_page'],
                'per_page' => $tasks['per_page'],
                'total' => $tasks['total'],
            ],
        ], 200); // OK
    }

    /**
     * Store a newly created task in storage.
     *
     * @param StoreTaskRequest $request Validated request object for storing a new task.
     * @return \Illuminate\Http\JsonResponse JSON response with the created task.
     */
    public function store(StoreTaskRequest $request)
    {
        $task = $this->TaskService->storeTask($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Task created successfully',
            'task' => $task,
        ], 201); // Created
    }

    /**
     * Display the specified task.
     *
     * @param string $id The ID of the task to retrieve.
     * @return \Illuminate\Http\JsonResponse JSON response containing the task data.
     */
    public function show(string $id )
    {
        $fetchedData = $this->TaskService->showTask($id );

        return response()->json([
            'status' => 'success',
            'message' => 'Task retrieved successfully',
            'task' => TaskResource::make($fetchedData),
        ], 200); // OK
    }

    /**
     * Update the specified task in storage.
     *
     * @param UpdateTaskRequest $request Validated request object containing updated task data.
     * @param string $id The ID of the task to update.
     * @return \Illuminate\Http\JsonResponse JSON response containing the updated task data.
     */
    public function update(UpdateTaskRequest $request, string $id)
    {
        $task = $this->TaskService->updateTask($request->validated(), $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Task updated successfully',
            'task' => TaskResource::make($task),
        ], 200); // OK
    }

    /**
     * Update the status of a task by the assigned user.
     *
     * This method is intended to allow the user assigned to the task to update the task's status.
     *
     * @param UpdateStatusRequest $request Validated request object containing the new status.
     * @param string $id The ID of the task whose status is to be updated.
     * @return \Illuminate\Http\JsonResponse JSON response containing the updated task.
     */
    public function updateByAssignedUser(UpdateStatusRequest $request, string $id)
    {
        $task = $this->TaskService->updateStatus($request->validated(), $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Task status updated successfully',
            'task' => TaskResource::make($task),
        ], 200); // OK
    }

    /**
     * Remove the specified task from storage.
     *
     * @param string $id The ID of the task to delete.
     * @return \Illuminate\Http\JsonResponse JSON response containing a success message.
     */
    public function destroy(string $id)
    {
        $message = $this->TaskService->deleteTask($id);

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], 200); // OK
    }

    public function restoreTask($id): \Illuminate\Http\JsonResponse
    {
        $message= $this->TaskService->restoreTask($id);
        return response()->json([
            'status' => $message['status'],
            'message' => $message['message'],
            'task' => new TaskResource($message['task']),
        ], 200); // OK
       
    }
}