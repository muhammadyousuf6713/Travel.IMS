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

        $isSuperAdmin = Auth::check() && (
            method_exists(Auth::user(), 'isSuperAdmin')
                ? Auth::user()->isSuperAdmin()
                : ((int) (Auth::user()->role_id ?? 0) === 1)
        );

        $isPreview = $request->has('preview') && $isSuperAdmin;

        // If the system is not suspended and user is not previewing as genuine Super-Admin, redirect to home
        if (!$status['is_suspended'] && !$isPreview) {
            return redirect('/');
        }

        return view('system_plan.suspended', compact('status', 'isPreview', 'isSuperAdmin'));
    }
}
