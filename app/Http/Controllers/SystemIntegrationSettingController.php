<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SystemIntegrationSetting;
use App\Services\LeadIntegrationService;
use App\User;
use App\sales_reference;
use App\inquirytypes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SystemIntegrationSettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the System Integration Settings dashboard
     */
    public function edit()
    {
        $settings = SystemIntegrationSetting::getSettings();
        $users = User::orderBy('name', 'asc')->get();
        $salesReferences = sales_reference::all();
        $inquiryTypes = inquirytypes::all();

        // Decode assigned user IDs
        $assignedIds = is_array($settings->assigned_user_ids) ? $settings->assigned_user_ids : [];
        
        // Active rotation pool
        $activeRotationUsers = User::whereIn('id', $assignedIds)->get()->sortBy(function ($user) use ($assignedIds) {
            return array_search($user->id, $assignedIds);
        });

        // Determine who is next in line
        $nextAgent = null;
        if (!empty($assignedIds)) {
            $nextIndex = ($settings->last_assigned_user_index + 1) % count($assignedIds);
            $nextAgentId = $assignedIds[$nextIndex] ?? null;
            $nextAgent = $nextAgentId ? User::find($nextAgentId) : null;
        }

        $webhookUrl = url('/api/v1/integrations/lead-webhook');
        $metaWebhookUrl = url('/api/v1/integrations/meta-leadgen');
        $publicFormUrl = url('/inquire-now');

        return view('integration_settings.edit', compact(
            'settings',
            'users',
            'salesReferences',
            'inquiryTypes',
            'assignedIds',
            'activeRotationUsers',
            'nextAgent',
            'webhookUrl',
            'metaWebhookUrl',
            'publicFormUrl'
        ));
    }

    /**
     * Update Integration Settings
     */
    public function update(Request $request)
    {
        $settings = SystemIntegrationSetting::getSettings();

        $validated = $request->validate([
            'auto_assign_enabled' => 'nullable',
            'assigned_user_ids' => 'nullable|array',
            'assigned_user_ids.*' => 'integer|exists:users,id',
            'default_sales_reference_id' => 'nullable|integer',
            'default_inquiry_type_id' => 'nullable|integer',
            'default_inquiry_status' => 'nullable|string|max:50',
            'meta_verify_token' => 'nullable|string|max:100',
            'meta_page_access_token' => 'nullable|string',
            'meta_app_id' => 'nullable|string|max:100',
            'meta_app_secret' => 'nullable|string|max:100',
            'public_form_enabled' => 'nullable',
            'public_form_title' => 'nullable|string|max:191',
            'public_form_subtitle' => 'nullable|string|max:255',
        ]);

        $settings->auto_assign_enabled = $request->has('auto_assign_enabled');
        $settings->public_form_enabled = $request->has('public_form_enabled');
        $settings->assigned_user_ids = $request->input('assigned_user_ids', []);
        $settings->default_sales_reference_id = $request->input('default_sales_reference_id');
        $settings->default_inquiry_type_id = $request->input('default_inquiry_type_id');
        $settings->default_inquiry_status = $request->input('default_inquiry_status', 'Open');
        $settings->meta_verify_token = $request->input('meta_verify_token') ?: 'travel_ims_' . Str::random(12);
        $settings->meta_page_access_token = $request->input('meta_page_access_token');
        $settings->meta_app_id = $request->input('meta_app_id');
        $settings->meta_app_secret = $request->input('meta_app_secret');
        $settings->public_form_title = $request->input('public_form_title', 'Travel Inquiry & Booking Request');
        $settings->public_form_subtitle = $request->input('public_form_subtitle', 'Submit your travel details and our specialist will contact you shortly.');

        $settings->save();

        return redirect()->route('integration_settings.edit')->with('success', 'System Integration & Lead Assignment Settings updated successfully.');
    }

    /**
     * Regenerate API Secret Key
     */
    public function regenerateApiKey()
    {
        $settings = SystemIntegrationSetting::getSettings();
        $settings->api_key = Str::random(48);
        $settings->save();

        return redirect()->route('integration_settings.edit')->with('success', 'API Secret Key regenerated successfully.');
    }

    /**
     * Simulate an Incoming Direct Lead to test the Round-Robin assignment
     */
    public function simulateLead(Request $request, LeadIntegrationService $leadService)
    {
        $mockNames = ['Ahmed Al-Farsi', 'Rashid Khan', 'Muhammad Usman', 'Zubair Shah', 'Farhan Siddiqui', 'Tariq Mehmood', 'Hamza Tariq'];
        $mockCities = ['Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Peshawar', 'Multan'];
        $mockServices = ['Umrah Package', 'Ramzan Umrah', 'Turkey Holiday Tour', 'Dubai Luxury Package', 'Baku City Tour', 'Executive Umrah 5-Star'];
        $mockSources = ['Facebook Lead Ads', 'Instagram Sponsored', 'Google Search Leads', 'Website Webhook', 'Meta Lead Ads'];

        $randomName = $mockNames[array_rand($mockNames)];
        $randomCity = $mockCities[array_rand($mockCities)];
        $randomService = $mockServices[array_rand($mockServices)];
        $randomSource = $mockSources[array_rand($mockSources)];

        $payload = [
            'name' => $randomName,
            'phone' => '+92 3' . rand(0, 4) . rand(0, 9) . ' ' . rand(1000000, 9999999),
            'email' => strtolower(str_replace(' ', '.', $randomName)) . rand(10, 99) . '@example.com',
            'city' => $randomCity,
            'service_type' => $randomService,
            'travel_date' => now()->addDays(rand(15, 60))->toDateString(),
            'adults' => rand(2, 4),
            'children' => rand(0, 2),
            'infants' => 0,
            'remarks' => 'Test Lead simulation triggered from System Integration Settings console. Looking for immediate booking quote.',
            'source' => $randomSource,
            'campaign_name' => 'Auto-Test Campaign 2026',
        ];

        $result = $leadService->ingestLead($payload);

        if ($request->ajax()) {
            return response()->json($result);
        }

        return redirect()->route('integration_settings.edit')->with('success', "Test Lead Ingested! Inquiry #{$result['inquiry_id']} automatically assigned to {$result['assigned_agent_name']} ({$result['source']}).");
    }
}
