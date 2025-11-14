<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Check if the user is authenticated and has the required role
        if (!Auth::check() || Auth::user()->role !== $role) {
            // If not, abort with a 403 Forbidden error
            abort(403, 'You are not authorised to do that');
        }

        return $next($request);
    }
}
