<?php

namespace App\Services;

use Exception;
use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

/**
 * Class ProjectService
 * 
 * This service handles operations related to projects, including fetching, storing, updating, and deleting projects.
 */
class ProjectService
{
    /**
     * Retrieve all projects with optional filters and sorting.
     * 
     * @param Request $request
     * The request object containing optional filters (priority, status) and sorting options (sort_order).
     * 
     * @return array
     * An array containing paginated project resources.
     * 
     * @throws \Exception
     * Throws an exception if there is an error during the process.
     */
    public function getAllProjects(): array
    {
        try {
            $projects=Project::with(['tasks'])->paginate(5);
            return[
                'data' => $projects->items(), // The items on the current page
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ];
          
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve projects: ' . $e->getMessage());
        }
    }

    /**
     * Store a new project.
     * 
     * @param array $data
     * An associative array containing the project's details (e.g., name, description, etc.).
     * 
     * @return Project
     * The created project resource.
     * 
     * @throws \Exception
     * Throws an exception if project creation fails.
     */
    public function storeProject(array $data): Project
    {
        try {
            $project = Project::create($data);

            if (!$project) {
                throw new Exception('Failed to create the project.');
            }

            return $project;
        } catch (Exception $e) {
            throw new Exception('Project creation failed: ' . $e->getMessage());
        }
    }

    /**
     * Retrieve a specific project by its ID.
     * 
     * @param int $id
     * The ID of the project to retrieve.
     * 
     * @return Project
     * The project resource.
     * 
     * @throws \Exception
     * Throws an exception if the project is not found.
     */
    public function showProject(int $id): Project
    {
        try {
            $project = Project::findOrFail($id);
            return $project;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Project not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve project: ' . $e->getMessage());
        }
    }

    public function MyProjectTasks($request): array
    {
        try {
            $id = Auth::id();
            $user = User::findOrFail($id);
            #TODO go back to project methods and think again of better solutions using hasMany
            // Get tasks through the user's projects (this should return a query builder instance)
            $tasksQuery = $user->tasksThroughProjects();
            
            $tasksQuery = $tasksQuery
                // Use scope to filter tasks by status if provided
                ->when($request->has('status'), function ($query) use ($request) {
                    return Project::whereRelation('tasks', 'status', $request->input('status'));
                })
                // Use scope to filter tasks by priority if provided
                ->when($request->has('priority'), function ($query) use ($request) {
                    return Project::whereRelation('tasks', 'priority', $request->input('priority'));

                })
                // Filter tasks by high priority and title input condition if provided
                ->when($request->has('title'), function ($query) use ($request) {
                    return $query->highPriorityWithTitle($request->input('title'));
                })->get()->toArray();

            // Fetch the project related to the tasks
            $projects = $user->projects()->with(['tasks'])->get();

            $oldestTask = null;
            $newestTask = null;

            foreach ($projects as $project) {
                // Get the oldest and newest tasks for each project
                $oldestTask = $project->oldestTask ? $project->oldestTask->toArray() : $oldestTask;
                $newestTask = $project->newestTask ? $project->newestTask->toArray() : $newestTask;
            }

            return [
                'tasks' => $tasksQuery, // Array of tasks
                'oldestTask' => $oldestTask,
                'newestTask' => $newestTask,
            ];
        } catch (ModelNotFoundException $e) {
            throw new Exception('Project not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to retrieve project: ' . $e->getMessage());
        }
    }

    /**
     * Update an existing project.
     * 
     * @param array $data
     * The data array containing the fields to update (e.g., name, status).
     * @param string $id
     * The ID of the project to update.
     * 
     * @return Project
     * The updated project resource.
     * 
     * @throws \Exception
     * Throws an exception if the project is not found or update fails.
     */
    public function updateProject(array $data, string $id): Project
    {
        try {
            $project = Project::findOrFail($id);
            $project->update(array_filter($data));

            return $project;
        } catch (ModelNotFoundException $e) {
            throw new Exception('Project not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to update project: ' . $e->getMessage());
        }
    }

    /**
     * Delete a project by its ID.
     * 
     * @param string $id
     * The ID of the project to delete.
     * 
     * @return string
     * A message confirming the successful deletion.
     * 
     * @throws \Exception
     * Throws an exception if the project is not found or deletion fails.
     */
    public function deleteProject(string $id): string
    {
        try {
            $project = Project::findOrFail($id);

            $project->delete();

            return "Project deleted successfully.";
        } catch (ModelNotFoundException $e) {
            throw new Exception('Project not found: ' . $e->getMessage());
        } catch (Exception $e) {
            throw new Exception('Failed to delete project: ' . $e->getMessage());
        }
    }

    /**
     * Restore a soft-deleted project by its ID.
     * 
     * @param int $id
     * The ID of the project to restore.
     * 
     * @return array
     * An array with the status and message of the operation.
     */
    public function restoreProject($id): array
    {
        try {
            $project = Project::withTrashed()->find($id);

            if (!$project) {
                throw new Exception('Project not found!');
            }

            if ($project->trashed()) {
                $project->restore();
            }

            return [
                'status' => 'success',
                'message' => 'Project restored successfully',
                'project' => $project,
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
