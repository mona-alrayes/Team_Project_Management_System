<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\ProjectUserService;
use App\Http\Requests\StoreUserToProjectRequest;
use App\Http\Requests\UpdateUserToProjectRequest;

class ProjectUserController extends Controller
{
    protected $projectUserService;

    public function __construct(ProjectUserService $projectUserService)
    {
        $this->projectUserService = $projectUserService;
    }

    // Add a user to a project
    public function addUserToProject(StoreUserToProjectRequest $request, $projectId)
    {
        $this->projectUserService->addUserToProject($projectId, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'User added to project successfully.',
        ], 200);
    }

    // Remove a user from a project
    public function removeUserFromProject($projectId, $userId)
    {
        $this->projectUserService->removeUserFromProject($projectId, $userId);

        return response()->json([
            'status' => 'success',
            'message' => 'User removed from project successfully.',
        ], 200);
    }

    // Update a user's role or contribution hours in a project
    public function updateUserInProject(UpdateUserToProjectRequest $request, $projectId, $userId)
    {
        $this->projectUserService->updateUserInProject($projectId, $userId, $request->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'User updated in project successfully.',
        ], 200);
    }

    // Show users in a project
    public function showUsersInProject($projectId)
    {
        $users = $this->projectUserService->showUsersInProject($projectId);

        return response()->json([
            'status' => 'success',
            'users' => $users,
        ], 200);
    }
}
