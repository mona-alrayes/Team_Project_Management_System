<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use App\Services\ProjectService;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;

class ProjectController extends Controller
{
    protected $ProjectService;

    /**
     * Constructor for ProjectController
     *
     * @param ProjectService $ProjectService The project service for handling project-related logic.
     */
    public function __construct(ProjectService $ProjectService)
    {
        $this->ProjectService = $ProjectService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projects = $this->ProjectService->getAllProjects($request);

        return response()->json([
            'status' => 'success',
            'message' => 'Projects retrieved successfully',
            'projects' => [
                'info' => ProjectResource::collection($projects['data']),
                'current_page' => $projects['current_page'],
                'last_page' => $projects['last_page'],
                'per_page' => $projects['per_page'],
                'total' => $projects['total'],
            ],
        ], 200); // OK
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request)
    {
        $project = $this->ProjectService->storeProject($request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Project created successfully',
            'project' => new ProjectResource($project),
        ], 201); // Created
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $project = $this->ProjectService->showProject($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Project retrieved successfully',
            'project' => ProjectResource::make($project),
        ], 200); // OK
    }

    public function showMyProjectTasks ( string $id , Request $request)
    {
        $project = $this->ProjectService->MyProjects($id , $request);

        return response()->json([
            'status' => 'success',
            'message' => 'Project with tasks retrieved successfully',
            'project' => ProjectResource::make($project),
        ], 200); // OK
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, string $id)
    {
        $project = $this->ProjectService->updateProject($request->validated(), $id);

        return response()->json([
            'status' => 'success',
            'message' => 'Project updated successfully',
            'project' => ProjectResource::make($project),
        ], 200); // OK
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $message = $this->ProjectService->deleteProject($id);

        return response()->json([
            'status' => 'success',
            'message' => $message,
        ], 200); // OK
    }

    /**
     * Restore a soft-deleted project.
     */
    public function restoreProject($id): \Illuminate\Http\JsonResponse
    {
        $message = $this->ProjectService->restoreProject($id);

        return response()->json([
            'status' => $message['status'],
            'message' => $message['message'],
            'project' => new ProjectResource($message['project']),
        ], 200); // OK
    }
}
