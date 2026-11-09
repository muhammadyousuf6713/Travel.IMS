<?php

namespace App\Http\Controllers;

use App\Services\SystemPlanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionsController extends Controller
{
    public function create()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $isSuperAdmin = method_exists($user, 'isSuperAdmin')
                ? $user->isSuperAdmin()
                : ((int) ($user->role_id ?? 0) === 1);

            $status = SystemPlanService::getStatus();
            $isSuspended = !empty($status['enabled']) && !empty($status['is_suspended']);

            if ($isSuspended && !$isSuperAdmin) {
                // Client account is locked out during suspension: log out so login page renders cleanly
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                return view('session.login-session');
            }

            return redirect($isSuperAdmin ? 'system-integration-settings' : 'inquiry');
        }

        return view('session.login-session');
    }

    public function store()
    {
        $attributes = request()->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $remember = request()->has('remember') ? (bool) request('remember') : true;

        if (Auth::attempt($attributes, $remember)) {
            session()->regenerate();

            $user = Auth::user();
            $isSuperAdmin = method_exists($user, 'isSuperAdmin')
                ? $user->isSuperAdmin()
                : ((int) ($user->role_id ?? 0) === 1);

            $status = SystemPlanService::getStatus();
            $isSuspended = !empty($status['enabled']) && !empty($status['is_suspended']);

            if ($isSuspended) {
                if ($isSuperAdmin) {
                    return redirect('system-integration-settings')->with(['success' => 'Welcome Super-Admin. The system subscription is currently suspended; manage the plan below.']);
                } else {
                    return redirect()->route('system.suspended');
                }
            }

            return redirect('inquiry')->with(['success' => 'You are logged in.']);
        } else {
            return back()->withErrors(['email' => 'Email or password invalid.']);
        }
    }

    public function destroy()
    {
        Auth::logout();

        return redirect('/login')->with(['success' => "You've been logged out."]);
    }
}
