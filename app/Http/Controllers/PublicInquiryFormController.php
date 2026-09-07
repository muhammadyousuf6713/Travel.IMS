<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SystemIntegrationSetting;
use App\Services\LeadIntegrationService;
use App\inquirytypes;
use Illuminate\Http\Request;

class PublicInquiryFormController extends Controller
{
    /**
     * Show Public Inquiry Form
     */
    public function showForm()
    {
        $settings = SystemIntegrationSetting::getSettings();
        if (!$settings->public_form_enabled) {
            return view('public_inquiry.disabled');
        }

        $inquiryTypes = inquirytypes::orderBy('type_name', 'asc')->get();

        return view('public_inquiry.form', compact('settings', 'inquiryTypes'));
    }

    /**
     * Process Public Inquiry Form Submission
     */
    public function submitForm(Request $request, LeadIntegrationService $leadService)
    {
        $settings = SystemIntegrationSetting::getSettings();
        if (!$settings->public_form_enabled) {
            return response()->json(['success' => false, 'message' => 'Inquiry submissions are currently disabled.'], 403);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:191',
            'customer_cell' => 'required|string|max:50',
            'customer_email' => 'nullable|email|max:191',
            'city' => 'nullable|string|max:100',
            'service_type' => 'required|string|max:191',
            'travel_date' => 'nullable|date|after_or_equal:today',
            'adults' => 'required|integer|min:1|max:50',
            'children' => 'nullable|integer|min:0|max:50',
            'infants' => 'nullable|integer|min:0|max:20',
            'requirements' => 'nullable|string|max:1000',
        ]);

        $payload = [
            'name' => $validated['customer_name'],
            'phone' => $validated['customer_cell'],
            'email' => $validated['customer_email'] ?? '',
            'city' => $validated['city'] ?? '',
            'service_type' => $validated['service_type'],
            'travel_date' => $validated['travel_date'] ?? null,
            'adults' => $validated['adults'],
            'children' => $validated['children'] ?? 0,
            'infants' => $validated['infants'] ?? 0,
            'remarks' => $validated['requirements'] ?? '',
            'source' => 'Website Inquiry Form',
            'campaign_name' => 'Direct Web Intake',
        ];

        $result = $leadService->ingestLead($payload);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your inquiry #' . $result['inquiry_id'] . ' has been received. Our travel specialist will get in touch with you shortly.',
                'data' => $result
            ]);
        }

        return redirect()->back()->with('success_inquiry', [
            'inquiry_id' => $result['inquiry_id'],
            'name' => $result['customer_name'],
        ]);
    }
}
