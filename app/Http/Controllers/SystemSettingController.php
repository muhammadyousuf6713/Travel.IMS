<?php

namespace App\Http\Controllers;

use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the System Settings control center.
     */
    public function index()
    {
        $user = Auth::user();
        $isAuthorized = $user->hasRole(['Admin', 'Super-Admin', 'admin', 'super-admin'])
            || $user->role_id == 1
            || $user->can('Organization Settings edit')
            || $user->can('Super-Admin');

        if (!$isAuthorized) {
            abort(403, 'Unauthorized access to system settings.');
        }

        $onlyAssignedInquiries = SystemSetting::get('only_assigned_inquiries', '0') === '1';

        return view('settings.index', compact('onlyAssignedInquiries'));
    }

    /**
     * Update the system settings.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $isAuthorized = $user->hasRole(['Admin', 'Super-Admin', 'admin', 'super-admin'])
            || $user->role_id == 1
            || $user->can('Organization Settings edit')
            || $user->can('Super-Admin');

        if (!$isAuthorized) {
            abort(403, 'Unauthorized access to system settings.');
        }

        // Toggle: Only show personal / assigned inquiries to sales agents
        $onlyAssigned = $request->boolean('only_assigned_inquiries') ? '1' : '0';
        SystemSetting::set('only_assigned_inquiries', $onlyAssigned);

        return redirect()->route('settings.index')->with('success', 'System settings saved successfully.');
    }
}
