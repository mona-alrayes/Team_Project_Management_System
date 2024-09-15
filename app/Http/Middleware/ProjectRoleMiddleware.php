<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;

class ProjectRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $role)
    {
        $user = Auth::user();

        // Get task from the route parameter
        $task = $request->route('task');

        if (!$task) {
            // Task doesn't exist, unauthorized
            abort(403, 'Unauthorized action.');
        }

        // Get the user's role in the project associated with the task
        $userProjectRole = $task->project->users()
            ->where('user_id', $user->id)
            ->first()
            ->pivot->role ?? null;

        // Check if the user has the required role
        if ($userProjectRole !== $role) {
            // User does not have the correct role, unauthorized
            abort(403, 'You do not have the required role to perform this action.');
        }

        return $next($request);
    }
}
