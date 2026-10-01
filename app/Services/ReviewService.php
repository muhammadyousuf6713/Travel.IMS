<?php

namespace App\Services;

use App\followup_remark;
use App\inquiry;
use App\Models\InquiryReview;
use App\Models\InquiryReviewFollowup;
use App\Models\SystemIntegrationSetting;
use App\Models\User;
use App\remarks;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Reviews module business rules:
 *  - which inquiries can enter Reviews (status Completed)
 *  - creating review records (manual "Sync to Reviews" or automatic on completion)
 *  - review-officer rotation (Integration Settings -> Review Sync)
 *  - logging follow-ups and moving the review status
 */
class ReviewService
{
    /** Inquiry statuses that count as "done" */
    public const ELIGIBLE_STATUSES = ['Completed'];

    private static ?bool $ready = null;

    /** True once the Reviews migrations have been run (keeps the app safe before `php artisan migrate`). */
    public static function isReady(): bool
    {
        if (self::$ready === null) {
            try {
                self::$ready = Schema::hasTable('inquiry_reviews')
                    && Schema::hasColumn('system_integration_settings', 'review_auto_sync_enabled');
            } catch (\Throwable $e) {
                self::$ready = false;
            }
        }
        return self::$ready;
    }

    /**
     * Counts for the "Sync to Reviews" modal.
     */
    public function preview(array $inquiryIds): array
    {
        $ids = $this->cleanIds($inquiryIds);
        $inquiries = inquiry::whereIn('id_inquiry', $ids)->get(['id_inquiry', 'status']);
        $existing = InquiryReview::withTrashed()->whereIn('inquiry_id', $ids)->pluck('inquiry_id')->map(fn ($v) => (int) $v)->all();

        $eligible = $already = $notCompleted = 0;
        foreach ($inquiries as $inq) {
            if (in_array((int) $inq->id_inquiry, $existing, true)) {
                $already++;
            } elseif (in_array($inq->status, self::ELIGIBLE_STATUSES, true)) {
                $eligible++;
            } else {
                $notCompleted++;
            }
        }

        return [
            'selected' => count($ids),
            'eligible' => $eligible,
            'already' => $already,
            'not_completed' => $notCompleted,
        ];
    }

    /**
     * Manual bulk sync from the Inquiry list.
     *
     * @param int|null $assignTo  user id, or null = use the rotation from settings
     */
    public function syncMany(array $inquiryIds, ?int $assignTo, ?int $byUserId): array
    {
        $ids = $this->cleanIds($inquiryIds);
        $created = $already = $notCompleted = 0;

        foreach (inquiry::whereIn('id_inquiry', $ids)->get() as $inq) {
            if (!in_array($inq->status, self::ELIGIBLE_STATUSES, true)) {
                $notCompleted++;
                continue;
            }
            $review = $this->createForInquiry($inq, $assignTo, 'manual', $byUserId);
            $review ? $created++ : $already++;
        }

        return ['created' => $created, 'already' => $already, 'not_completed' => $notCompleted];
    }

    /**
     * Create the review record for one inquiry. Returns null if it is already in Reviews.
     */
    public function createForInquiry(inquiry $inq, ?int $assignTo, string $source, ?int $byUserId, ?Carbon $completedAt = null): ?InquiryReview
    {
        if (InquiryReview::withTrashed()->where('inquiry_id', $inq->id_inquiry)->exists()) {
            return null;
        }

        $settings = SystemIntegrationSetting::getSettings();
        $completedAt = $completedAt ?: $this->guessCompletedAt($inq);
        $days = max(0, (int) ($settings->review_due_after_days ?? 3));
        $due = $completedAt->copy()->startOfDay()->addDays($days);
        $nextFollowup = $due->lt(Carbon::today()) ? Carbon::today() : $due;

        $assignee = $assignTo ?: $this->nextAssignee($settings);

        try {
            return InquiryReview::create([
                'inquiry_id' => $inq->id_inquiry,
                'customer_id' => $inq->customer_id,
                'assigned_to' => $assignee,
                'assigned_by' => $byUserId,
                'source' => $source,
                'status' => 'pending',
                'completed_at' => $completedAt,
                'due_date' => $due->toDateString(),
                'next_followup_date' => $nextFollowup->toDateString(),
                'attempts' => 0,
                'created_by' => $byUserId,
            ]);
        } catch (QueryException $e) {
            // Unique inquiry_id: another request synced it at the same moment
            if (InquiryReview::withTrashed()->where('inquiry_id', $inq->id_inquiry)->exists()) {
                return null;
            }
            throw $e;
        }
    }

    /**
     * Next review officer from the rotation pool (Integration Settings). Null when no pool is set.
     */
    public function nextAssignee(?SystemIntegrationSetting $settings = null): ?int
    {
        $settings = $settings ?: SystemIntegrationSetting::getSettings();
        $pool = is_array($settings->review_assignee_ids) ? array_values($settings->review_assignee_ids) : [];
        $pool = array_values(array_intersect(array_map('intval', $pool), User::pluck('id')->map(fn ($v) => (int) $v)->all()));

        if (count($pool) === 0) {
            return null;
        }

        $index = ((int) ($settings->review_last_assigned_index ?? -1) + 1) % count($pool);
        $settings->review_last_assigned_index = $index;
        $settings->save();

        return $pool[$index];
    }

    /**
     * Log a contact attempt / result and update the review.
     */
    public function logFollowup(InquiryReview $review, array $data, int $userId): InquiryReviewFollowup
    {
        return DB::transaction(function () use ($review, $data, $userId) {
            $outcome = $data['outcome'];
            $platforms = array_values(array_filter((array) ($data['platforms'] ?? [])));
            if (in_array($outcome, ['received', 'declined'], true)) {
                $data['next_followup_date'] = null; // a closed review has no next follow-up
            }

            $followup = InquiryReviewFollowup::create([
                'review_id' => $review->id,
                'user_id' => $userId,
                'channel' => $data['channel'] ?? null,
                'outcome' => $outcome,
                'rating' => $data['rating'] ?? null,
                'platforms' => $platforms ?: null,
                'review_link' => $data['review_link'] ?? null,
                'remarks' => $data['remarks'] ?? null,
                'next_followup_date' => $data['next_followup_date'] ?? null,
            ]);

            if ($outcome !== 'note') {
                $review->attempts = (int) $review->attempts + 1;
                $review->last_contacted_at = now();
            }

            switch ($outcome) {
                case 'link_sent':
                case 'will_do_later':
                    $review->status = 'requested';
                    break;
                case 'not_reachable':
                    $review->status = 'not_reachable';
                    break;
                case 'received':
                    $review->status = 'received';
                    $review->received_at = now();
                    $review->rating = $data['rating'] ?? $review->rating;
                    $review->platforms = $platforms ?: $review->platforms;
                    $review->review_link = $data['review_link'] ?? $review->review_link;
                    $review->customer_comment = $data['customer_comment'] ?? $review->customer_comment;
                    break;
                case 'declined':
                    $review->status = 'declined';
                    break;
            }

            if (in_array($review->status, InquiryReview::OPEN_STATUSES, true)) {
                if (!empty($data['next_followup_date'])) {
                    $review->next_followup_date = $data['next_followup_date'];
                }
            } else {
                $review->next_followup_date = null;
            }

            $review->save();

            return $followup;
        });
    }

    public function reassign(array $reviewIds, ?int $userId, ?int $byUserId): int
    {
        return InquiryReview::whereIn('id', $this->cleanIds($reviewIds))
            ->update(['assigned_to' => $userId, 'assigned_by' => $byUserId, 'updated_at' => now()]);
    }

    /**
     * Best guess of when the inquiry was completed: latest "Completed" follow-up/remark,
     * otherwise the inquiry's updated_at (stored as text in this DB), otherwise now.
     */
    public function guessCompletedAt(inquiry $inq): Carbon
    {
        $candidates = [];

        try {
            $fr = followup_remark::where('inquiry_id', $inq->id_inquiry)
                ->where('followup_status', 'Completed')->max('created_at');
            if ($fr) {
                $candidates[] = Carbon::parse($fr);
            }
        } catch (\Throwable $e) {
        }

        try {
            $rm = remarks::where('inquiry_id', $inq->id_inquiry)
                ->whereIn('remarks_status', ['4', 'Completed'])->max('created_at');
            if ($rm) {
                $candidates[] = Carbon::parse($rm);
            }
        } catch (\Throwable $e) {
        }

        if ($candidates) {
            return collect($candidates)->max();
        }

        try {
            $raw = $inq->getRawOriginal('updated_at');
            if (!empty($raw)) {
                return Carbon::parse($raw);
            }
        } catch (\Throwable $e) {
        }

        return now();
    }

    private function cleanIds(array $ids): array
    {
        return array_values(array_unique(array_filter(array_map('intval', $ids))));
    }
}
