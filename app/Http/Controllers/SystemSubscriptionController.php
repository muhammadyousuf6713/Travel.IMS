<?php

namespace App\Http\Controllers;

use App\Services\SystemPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemSubscriptionController extends Controller
{
    /**
     * Display the System Subscription Suspended / Lock Screen.
     */
    public function suspended(Request $request)
    {
        $status = SystemPlanService::getStatus();

        // If the system is not suspended and user is not previewing as genuine Super-Admin, redirect to home
        $isPreview = $request->has('preview') && Auth::check() && (
            method_exists(Auth::user(), 'isSuperAdmin')
                ? Auth::user()->isSuperAdmin()
                : ((int) (Auth::user()->role_id ?? 0) === 1)
        );

        if (!$status['is_suspended'] && !$isPreview) {
            return redirect('/');
        }

        return view('system_plan.suspended', compact('status', 'isPreview'));
    }

    /**
     * Handle transition to login for the System Owner from the suspended screen.
     * Clears non-superadmin session so client/admin is cleanly logged out and the login form renders.
     */
    public function ownerLogin(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $isSuperAdmin = method_exists($user, 'isSuperAdmin')
                ? $user->isSuperAdmin()
                : ((int) ($user->role_id ?? 0) === 1);

            // If already logged in as Super-Admin, proceed directly to Integration Settings
            if ($isSuperAdmin) {
                return redirect()->route('integration_settings.edit')
                    ->with('info', 'Welcome Super-Admin. You can manage or extend the system plan below.');
            }

            // Client / employee is logged in; terminate their session so Owner can enter credentials
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }

        return redirect()->route('login')
            ->with('info', 'Please sign in with your Super-Admin credentials to manage the system subscription.');
    }
}
