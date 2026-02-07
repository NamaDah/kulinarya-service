<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            abort(401, 'Unauthenticated.');
        }

        // Super admin bypasses all role checks
        if ($request->user()->isSuperAdmin()) {
            return $next($request);
        }

        if (!$request->user()->hasAnyRole($roles)) {
            abort(403, 'You do not have the required role to access this resource.');
        }

        return $next($request);
    }
}
