<?php

namespace App\Observers;

use App\inquiry;
use App\Models\SystemIntegrationSetting;
use App\Services\ReviewService;
use Illuminate\Support\Facades\Log;

/**
 * Automatic Review Sync: when an inquiry's status changes to Completed and
 * "Automatic Review Sync" is ON in Integration Settings, create its review record.
 *
 * Never throws: a problem here must not stop the inquiry from saving.
 */
class InquiryReviewObserver
{
    public function updated(inquiry $inquiry): void
    {
        try {
            if (!$inquiry->wasChanged('status')
                || !in_array($inquiry->status, ReviewService::ELIGIBLE_STATUSES, true)
                || !ReviewService::isReady()) {
                return;
            }

            $settings = SystemIntegrationSetting::getSettings();
            if (!$settings->review_auto_sync_enabled) {
                return;
            }

            app(ReviewService::class)->createForInquiry($inquiry, null, 'auto', auth()->id(), now());
        } catch (\Throwable $e) {
            Log::warning('Automatic review sync failed for inquiry #' . $inquiry->id_inquiry . ': ' . $e->getMessage());
        }
    }
}
