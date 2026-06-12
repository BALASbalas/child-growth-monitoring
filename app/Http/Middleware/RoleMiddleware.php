<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            // Return JSON for API/AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthenticated.'
                ], 401);
            }
            return redirect()->route('login');
        }

        $user = Auth::user();

        // If no specific role is required, allow all authenticated users
        if (empty($roles)) {
            return $next($request);
        }

        // Check if user has any of the allowed roles
        if (!in_array($user->role, $roles)) {
            // Return JSON for API/AJAX requests
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. You do not have permission to perform this action.'
                ], 403);
            }

            // Redirect to user's own dashboard with an error message
            $redirectRoute = $this->getDashboardRoute($user->role);
            return redirect()->route($redirectRoute)
                ->with('error', 'Huna ruhusa ya kufungua ukurasa huu. (You do not have permission to access this page.)');
        }

        return $next($request);
    }

    /**
     * Get the dashboard route for a given role.
     */
    private function getDashboardRoute(string $role): string
    {
        return match ($role) {
            'admin' => 'admin.dashboard',
            'nurse' => 'nurse.dashboard',
            'doctor' => 'doctor.dashboard',
            'parent' => 'parent.dashboard',
            'guardian' => 'guardian.dashboard',
            default => 'dashboard',
        };
    }
}