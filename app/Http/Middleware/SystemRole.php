<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class SystemRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  $roles
     * @return mixed
     */
    public function handle($request, Closure $next, ...$roles)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // Get the user's role
            $userRole = Auth::user()->system_role;

            // Check if the user's role is in the allowed roles
            if (in_array($userRole, $roles)) {
                return $next($request); // Allow access
            }
        }

        // If the user doesn't have the required role, deny access
        return response()->json(['error' => 'Unauthorized'], 403);
    }
}
