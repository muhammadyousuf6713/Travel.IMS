<?php

namespace App\Http\Middleware;

use App\Services\SystemPlanService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;

class CheckSystemSubscriptionPlan
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $status = SystemPlanService::getStatus();

        // Share system plan notice with all views (for sticky alert banners)
        View::share('systemPlanNotice', $status);

        // If plan is not enabled, do nothing (no lockout, no interruptions)
        if (!$status['enabled'] || !$status['is_configured']) {
            return $next($request);
        }

        // Exempt routes / paths
        if ($this->isExemptRoute($request)) {
            return $next($request);
        }

        // Super-Admin users are NEVER locked out (so the owner can always log in and manage the plan)
        if (Auth::check()) {
            $user = Auth::user();
            if ((int) ($user->role_id ?? 0) === 1 || 
                (method_exists($user, 'hasRole') && $user->hasRole(['Super-Admin', 'Super Admin'])) ||
                (method_exists($user, 'can') && $user->can('Super-Admin'))) {
                return $next($request);
            }
        }

        // If the system has passed the deadline (+ grace period), enforce suspension
        if ($status['is_suspended']) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'System access is suspended due to subscription renewal requirement. Please contact the system owner.',
                    'system_suspended' => true,
                ], 423);
            }

            return redirect()->route('system.suspended');
        }

        return $next($request);
    }

    /**
     * Check if the current request is exempt from suspension lockout.
     */
    protected function isExemptRoute(Request $request): bool
    {
        $path = $request->path();

        // System suspension screen
        if ($request->routeIs('system.suspended') || $path === 'system-suspended') {
            return true;
        }

        // Authentication & session routes
        if ($request->is('login', 'logout', 'session', 'session/*', 'reset-password/*', 'forgot-password')) {
            return true;
        }

        // Assets and static resources
        if ($request->is('assets/*', 'css/*', 'js/*', 'images/*', 'favicon.*')) {
            return true;
        }

        return false;
    }
}
