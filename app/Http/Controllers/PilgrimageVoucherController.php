<?php

namespace App\Http\Controllers;

use App\Models\PilgrimageBooking;
use App\Models\OrganizationSetting;
use App\Models\PilgrimageSetting;
use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PilgrimageVoucherController extends Controller
{
    /**
     * Display the official Hajj/Umrah Service Voucher for printing / PDF view.
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
        $currencies = Currency::getActiveCurrencies();

        return view('pilgrimage.voucher.print', compact(
            'booking',
            'orgSetting',
            'settings',
            'voucherNo',
            'printDate',
            'user',
            'currencies'
        ));
    }
}
