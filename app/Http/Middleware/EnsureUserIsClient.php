<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;


class EnsureUserIsClient
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || Auth::user()->user_type !== 'CLIENT') {
            // abort(403, 'Unauthorized. You must be a client to access this page.');
            return back()->with('error', 'Unauthorized. You must be a client to access this page.');
        }
        return $next($request);
    }
}
