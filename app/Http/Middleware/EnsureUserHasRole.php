<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string ...$roles  // Accept one or more role names
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        // Check if user is logged in and has a role
        if (!Auth::check() || !$request->user()->role) {
            return redirect('login');
        }

        // Check if the user's role is in the list of allowed roles
        foreach ($roles as $role) {
            if ($request->user()->role->name == $role) {
                return $next($request);
            }
        }

        // If no role matched, abort with a 403 Forbidden error
        abort(403, 'Unauthorized action.');
    }
}
