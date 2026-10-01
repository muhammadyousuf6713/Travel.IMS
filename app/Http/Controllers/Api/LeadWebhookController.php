<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SystemIntegrationSetting;
use App\Services\LeadIntegrationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LeadWebhookController extends Controller
{
    /**
     * Handle Meta / Facebook Lead Ads Webhook
     */
    public function metaWebhook(Request $request, LeadIntegrationService $leadService)
    {
        $settings = SystemIntegrationSetting::getSettings();

        // 1. Verification Handshake (GET Request from Meta)
        if ($request->isMethod('get')) {
            $mode = $request->query('hub_mode');
            $verifyToken = $request->query('hub_verify_token');
            $challenge = $request->query('hub_challenge');

            if ($mode === 'subscribe' && $verifyToken === $settings->meta_verify_token) {
                Log::info('Meta Webhook verified successfully.');
                return response($challenge, 200)->header('Content-Type', 'text/plain');
            }

            Log::warning('Meta Webhook verification failed. Received token: ' . $verifyToken);
            return response('Forbidden', 403);
        }

        // 2. Event Notification (POST Request from Meta)
        // Verify the request really comes from Meta (enabled once the App Secret is saved in Integration Settings)
        if (!empty($settings->meta_app_secret)) {
            $signature = (string) $request->header('X-Hub-Signature-256', '');
            $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $settings->meta_app_secret);
            if ($signature === '' || !hash_equals($expected, $signature)) {
                Log::warning('Meta Webhook rejected: invalid or missing X-Hub-Signature-256 header.');
                return response()->json(['status' => 'INVALID_SIGNATURE'], 403);
            }
        }

        try {
            $payload = $request->all();
            Log::info('Meta Leadgen Webhook Received: ' . json_encode($payload));

            $result = $leadService->processMetaWebhook($payload);
            return response()->json(['status' => 'EVENT_RECEIVED', 'result' => $result], 200);
        } catch (\Throwable $e) {
            Log::error('Meta Webhook Processing Error: ' . $e->getMessage());
            return response()->json(['status' => 'ERROR', 'message' => 'Internal error while processing the event.'], 500);
        }
    }

    /**
     * Handle Direct Lead Ingestion Webhook / API
     */
    public function directWebhook(Request $request, LeadIntegrationService $leadService)
    {
        $settings = SystemIntegrationSetting::getSettings();

        // 1. Authenticate Request via API Key
        $providedKey = $request->header('X-API-KEY') 
            ?: $request->bearerToken() 
            ?: $request->input('api_key');

        if (empty($providedKey) || $providedKey !== $settings->api_key) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Invalid or missing API Secret Key.'
            ], 401);
        }

        // 2. Validate payload
        $validated = $request->validate([
            'name' => 'nullable|string|max:191',
            'customer_name' => 'nullable|string|max:191',
            'phone' => 'nullable|string|max:50',
            'customer_cell' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:191',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'service_type' => 'nullable|string|max:191',
            'travel_date' => 'nullable|date',
            'adults' => 'nullable|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'infants' => 'nullable|integer|min:0',
            'remarks' => 'nullable|string',
            'message' => 'nullable|string',
            'source' => 'nullable|string|max:100',
            'campaign_name' => 'nullable|string|max:191',
        ]);

        try {
            $result = $leadService->ingestLead($request->all());
            return response()->json([
                'success' => true,
                'message' => 'Lead received and inquiry created successfully.',
                'data' => $result
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Direct Webhook Lead Ingestion Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lead ingestion failed due to an internal error.'
            ], 500);
        }
    }
}
