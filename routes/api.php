<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// System Integration & Lead Ingestion Webhooks
Route::prefix('v1/integrations')->group(function () {
    // Meta / Facebook Lead Ads Webhook
    Route::match(['get', 'post'], '/meta-leadgen', [\App\Http\Controllers\Api\LeadWebhookController::class, 'metaWebhook'])->name('api.integrations.meta_leadgen');
    
    // Direct REST API / External Webhook Ingestion (secured by API Key)
    Route::post('/lead-webhook', [\App\Http\Controllers\Api\LeadWebhookController::class, 'directWebhook'])->name('api.integrations.lead_webhook');
});
