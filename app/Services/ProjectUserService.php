<?php

namespace App\Services;

use App\Models\Project;

class ProjectUserService
{
    public function addUserToProject($projectId, array $data)
    {
        $project = Project::findOrFail($projectId);
        $project->users()->attach($data['user_id'], [
            'role' => $data['role'] ?? 'developer', // Default to 'developer' if no role is provided
            'created_at' => now(),
            'updated_at' => now(),
            'last_activity' => now(),
            'distribution_hours' => null,
        ]);
    }

    public function removeUserFromProject($projectId, $userId)
    {
        $project = Project::findOrFail($projectId);
        $project->users()->detach($userId);
    }

    public function updateUserInProject($projectId, $userId, array $data)
    {
        $project = Project::findOrFail($projectId);
        $project->users()->updateExistingPivot($userId, $data);
    }

    public function showUsersInProject($projectId)
    {
        $project = Project::findOrFail($projectId);
        return $project->users()->get();
    }
}
