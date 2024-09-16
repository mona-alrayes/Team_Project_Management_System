<?php

namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectUserController extends Controller
{
    // Add a user to a project
    public function store(Request $request, $projectId)
    {
        #TODO: separate the code into request validation and service 
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|string|in:manager,developer,tester',
        ]);

        $project = Project::findOrFail($projectId);
        $project->users()->attach($validated['user_id'], [
            'role' => $validated['role'],
            'created_at' => now(),
            'updated_at' => now(),
            'last_activity' => now(),
            'distribution_hours'=> null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User added to project successfully.',
            'info' => [
                'project' => $project->name,
                'user' =>$validated['user_id'],
                'role' => $validated['role'],
            ]
        ], 200);
    }

    // Remove a user from a project
    public function destroy($projectId, $userId)
    {
        $project = Project::findOrFail($projectId);
        $project->users()->detach($userId);

        return response()->json([
            'status' => 'success',
            'message' => 'User removed from project successfully.',
        ], 200);
    }

    // Update a user's role or contribution hours in a project
    public function update(Request $request, $projectId, $userId)
    {
        $validated = $request->validate([
            'role' => 'nullable|string|in:manager,developer,tester',
        ]);

        $project = Project::findOrFail($projectId);
        $project->users()->updateExistingPivot($userId, $validated);

        return response()->json([
            'status' => 'success',
            'message' => 'User updated in project successfully.',
        ], 200);
    }

    // Show users in a project
    public function index($projectId)
    {
        $project = Project::findOrFail($projectId);
        $users = $project->users()->get();

        return response()->json([
            'status' => 'success',
            'users' => $users,
        ], 200);
    }
}

