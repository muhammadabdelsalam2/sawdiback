<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('SuperAdmin')) {
            return $next($request);
        }

        if (property_exists($user, 'role') && strtolower((string) $user->role) === 'superadmin') {
            return $next($request);
        }

        $defaultAdminEmail = 'admin@elsawady.com';
        if (strtolower($user->email ?? '') === $defaultAdminEmail) {
            return $next($request);
        }

        abort(403);
    }
}
