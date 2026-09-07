<?php

namespace App\Http\Controllers;

use App\Models\PilgrimageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemIntegrationSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display System Integration Settings.
     */
    public function edit()
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        if (!$isBossOrAdmin) {
            abort(403, 'Unauthorized. Only administrators can access System Integration Settings.');
        }

        $settings = PilgrimageSetting::getSettings();

        return view('integration_settings.edit', compact('settings'));
    }

    /**
     * Update System Integration Settings.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        if (!$isBossOrAdmin) {
            abort(403, 'Unauthorized.');
        }

        $request->validate([
            'makkah_ops_contact' => 'required|string|max:100',
            'madinah_ops_contact' => 'required|string|max:100',
            'jeddah_ops_contact' => 'required|string|max:100',
            'general_hotline' => 'required|string|max:100',
            'ziyaraat_offset_hours' => 'required|integer|min:1|max:168',
            'reminder_window_hours' => 'required|integer|min:1|max:168',
            'reminder_repeat_hours' => 'required|integer|min:1|max:24',
        ]);

        $settings = PilgrimageSetting::getSettings();
        $settings->update($request->only([
            'makkah_ops_contact',
            'madinah_ops_contact',
            'jeddah_ops_contact',
            'general_hotline',
            'ziyaraat_offset_hours',
            'reminder_window_hours',
            'reminder_repeat_hours',
        ]));

        return back()->with('success', 'System integration settings saved successfully.');
    }
}
