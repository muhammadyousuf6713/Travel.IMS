<?php

namespace App\Http\Controllers;

use App\Models\OrganizationSetting;
use App\Models\PilgrimageBooking;
use App\Models\PilgrimageSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PilgrimageVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * View or Print Hotel Accommodation Voucher.
     */
    public function show($id)
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        $booking = PilgrimageBooking::with([
            'submittingAgent',
            'pilgrims',
            'hotels' => function ($q) {
                $q->orderBy('check_in', 'asc');
            },
            'flights',
        ])->findOrFail($id);

        if (!$isBossOrAdmin && $booking->submitting_agent_id !== $user->id) {
            abort(403, 'Unauthorized access to this voucher.');
        }

        $orgSetting = OrganizationSetting::first();
        $settings = PilgrimageSetting::getSettings();
        $voucherNo = 'HV-' . $booking->crm_id;
        $printDate = Carbon::now();

        return view('pilgrimage.voucher.print', compact(
            'booking',
            'orgSetting',
            'settings',
            'voucherNo',
            'printDate',
            'user'
        ));
    }
}
