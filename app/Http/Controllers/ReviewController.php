<?php

namespace App\Http\Controllers;

use App\Models\InquiryReview;
use App\Models\InquiryReviewFollowup;
use App\Models\OrganizationSetting;
use App\Models\SystemIntegrationSetting;
use App\Models\User;
use App\Services\ReviewService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviews)
    {
        $this->middleware('auth');
    }

    /* ------------------------------------------------------------------
     |  Reviews page
     * ------------------------------------------------------------------ */

    public function index()
    {
        if (!ReviewService::isReady()) {
            return redirect('dashboard')->with('error', 'Reviews module is not set up yet. Please run: php artisan migrate');
        }

        $canSeeAll = $this->canSeeAll();
        $counts = $this->buildCounts();
        $users = User::orderBy('name')->get(['id', 'name']);
        $outcomes = InquiryReviewFollowup::OUTCOMES;
        $channels = InquiryReviewFollowup::CHANNELS;
        $platforms = InquiryReview::PLATFORMS;

        return view('reviews.index', compact('canSeeAll', 'counts', 'users', 'outcomes', 'channels', 'platforms'));
    }

    public function counts()
    {
        return response()->json($this->buildCounts());
    }

    public function data(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $open = InquiryReview::OPEN_STATUSES;

        $query = InquiryReview::query()
            ->leftJoin('inquiry', 'inquiry.id_inquiry', '=', 'inquiry_reviews.inquiry_id')
            ->leftJoin('customers', 'customers.id_customers', '=', 'inquiry_reviews.customer_id')
            ->leftJoin('users as assignee', 'assignee.id', '=', 'inquiry_reviews.assigned_to')
            ->leftJoin('inquirytypes', 'inquirytypes.type_id', '=', 'inquiry.inquiry_type')
            ->select([
                'inquiry_reviews.*',
                'customers.customer_name',
                'customers.customer_cell',
                'customers.whatsapp_number',
                'inquiry.travel_date',
                'inquirytypes.type_name as inquiry_type_name',
                'assignee.name as assignee_name',
            ]);

        $this->applyScope($query);

        switch ($request->input('tab', 'open')) {
            case 'open':
                $query->whereIn('inquiry_reviews.status', $open);
                break;
            case 'due_today':
                $query->whereIn('inquiry_reviews.status', $open)->whereDate('inquiry_reviews.next_followup_date', $today);
                break;
            case 'overdue':
                $query->whereIn('inquiry_reviews.status', $open)->whereDate('inquiry_reviews.next_followup_date', '<', $today);
                break;
            case 'pending':
            case 'requested':
            case 'not_reachable':
            case 'received':
            case 'declined':
                $query->where('inquiry_reviews.status', $request->input('tab'));
                break;
            // 'all' => no filter
        }

        if ($request->filled('assigned_to') && $this->canSeeAll()) {
            $request->input('assigned_to') === 'none'
                ? $query->whereNull('inquiry_reviews.assigned_to')
                : $query->where('inquiry_reviews.assigned_to', (int) $request->input('assigned_to'));
        }

        $settings = SystemIntegrationSetting::getSettings();
        $company = $this->companyName();
        $canSeeAll = $this->canSeeAll();

        return DataTables::of($query)
            ->addColumn('checkbox', fn ($r) => $canSeeAll
                ? '<input type="checkbox" class="form-check-input review-checkbox" value="' . $r->id . '">'
                : '')
            ->addColumn('customer', function ($r) {
                $name = $r->customer_name ?: 'Unknown customer';
                $phone = $r->whatsapp_number ?: $r->customer_cell;
                return '<div class="rv-person">'
                    . $this->avatar($name)
                    . '<div class="min-w-0">'
                    . '<div class="rv-name text-truncate">' . e($name) . '</div>'
                    . '<div class="rv-sub">'
                    . ($phone ? '<span>' . e($phone) . '</span><span class="rv-dot"></span>' : '')
                    . '<a href="' . url('edit_inquiry/' . $r->inquiry_id) . '" target="_blank" class="rv-link">Inquiry #' . $r->inquiry_id . '</a>'
                    . ($r->inquiry_type_name ? '<span class="rv-dot"></span><span>' . e($r->inquiry_type_name) . '</span>' : '')
                    . '</div></div></div>';
            })
            ->addColumn('completed', function ($r) {
                $done = $r->completed_at ? $r->completed_at->format('d M Y') : '—';
                $travel = $this->fmtDate($r->travel_date);
                return '<div class="rv-date">' . $done . '</div>'
                    . '<div class="rv-sub">Travel ' . $travel . '</div>';
            })
            ->addColumn('followup', function ($r) {
                if (!$r->isOpen() || !$r->next_followup_date) {
                    return '<span class="rv-muted">—</span>';
                }
                $days = Carbon::today()->diffInDays($r->next_followup_date, false);
                if ($days < 0) {
                    $n = abs((int) $days);
                    $hint = '<span class="rv-due overdue">Overdue by ' . $n . ' day' . ($n > 1 ? 's' : '') . '</span>';
                } elseif ((int) $days === 0) {
                    $hint = '<span class="rv-due today">Today</span>';
                } else {
                    $n = (int) $days;
                    $hint = '<span class="rv-due">In ' . $n . ' day' . ($n > 1 ? 's' : '') . '</span>';
                }
                return '<div class="rv-date">' . $r->next_followup_date->format('d M Y') . '</div>' . $hint;
            })
            ->addColumn('assignee', function ($r) {
                if (!$r->assignee_name) {
                    return '<span class="rv-badge rv-unassigned">Unassigned</span>';
                }
                return '<div class="rv-person">' . $this->avatar($r->assignee_name, true)
                    . '<span class="rv-assignee text-truncate">' . e($r->assignee_name) . '</span></div>';
            })
            ->addColumn('status_badge', fn ($r) => $this->statusBadge($r->status))
            ->addColumn('result', function ($r) {
                if ($r->status === 'received') {
                    $stars = '';
                    if ($r->rating) {
                        for ($i = 1; $i <= 5; $i++) {
                            $stars .= '<i class="fa-' . ($i <= (int) $r->rating ? 'solid' : 'regular') . ' fa-star"></i>';
                        }
                    }
                    $plat = $r->platforms ? implode(', ', $r->platforms) : '';
                    return ($stars ? '<div class="rv-stars">' . $stars . '</div>' : '')
                        . '<div class="rv-sub">' . e($plat ?: 'Review received') . '</div>';
                }
                $n = (int) $r->attempts;
                $last = $r->last_contacted_at ? 'Last ' . $r->last_contacted_at->format('d M') : 'Not contacted yet';
                return '<div class="rv-date">' . $n . ' attempt' . ($n === 1 ? '' : 's') . '</div><div class="rv-sub">' . $last . '</div>';
            })
            ->addColumn('actions', function ($r) use ($settings, $company) {
                $name = e($r->customer_name);
                $html = '<div class="rv-actions">';
                $wa = $this->whatsappUrl($r, $settings, $company);
                if ($wa) {
                    $html .= '<a href="' . e($wa) . '" target="_blank" class="rv-btn rv-btn-wa" data-bs-toggle="tooltip" title="Send review request on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>';
                } else {
                    $html .= '<span class="rv-btn rv-btn-disabled" data-bs-toggle="tooltip" title="No valid phone number"><i class="fa-brands fa-whatsapp"></i></span>';
                }
                $html .= '<button type="button" class="rv-btn rv-btn-primary log-followup" data-id="' . $r->id . '" data-customer="' . $name . '"><i class="fa-regular fa-pen-to-square"></i><span>Follow-up</span></button>';
                $html .= '<button type="button" class="rv-btn view-history" data-id="' . $r->id . '" data-customer="' . $name . '" data-bs-toggle="tooltip" title="History"><i class="fa-solid fa-clock-rotate-left"></i></button>';
                return $html . '</div>';
            })
            ->filterColumn('customer', function ($q, $kw) {
                $q->where(function ($w) use ($kw) {
                    $w->where('customers.customer_name', 'like', "%{$kw}%")
                        ->orWhere('customers.customer_cell', 'like', "%{$kw}%")
                        ->orWhere('customers.whatsapp_number', 'like', "%{$kw}%")
                        ->orWhere('inquiry_reviews.inquiry_id', 'like', "%{$kw}%");
                });
            })
            ->filterColumn('assignee', fn ($q, $kw) => $q->where('assignee.name', 'like', "%{$kw}%"))
            ->orderColumn('customer', 'customers.customer_name $1')
            ->orderColumn('completed', 'inquiry_reviews.completed_at $1')
            ->orderColumn('followup', 'inquiry_reviews.next_followup_date $1')
            ->rawColumns(['checkbox', 'customer', 'completed', 'followup', 'assignee', 'status_badge', 'result', 'actions'])
            ->make(true);
    }

    /* ------------------------------------------------------------------
     |  Sync to Reviews (Inquiry list)
     * ------------------------------------------------------------------ */

    public function syncPreview(Request $request)
    {
        if (!ReviewService::isReady()) {
            return response()->json(['success' => false, 'message' => 'Reviews module is not set up yet. Please run: php artisan migrate'], 422);
        }
        $request->validate(['inquiry_ids' => 'required|array', 'inquiry_ids.*' => 'integer']);

        $settings = SystemIntegrationSetting::getSettings();
        $pool = is_array($settings->review_assignee_ids) ? $settings->review_assignee_ids : [];

        return response()->json(array_merge(
            ['success' => true, 'rotation_count' => count($pool)],
            $this->reviews->preview($request->input('inquiry_ids'))
        ));
    }

    public function sync(Request $request)
    {
        if (!ReviewService::isReady()) {
            return response()->json(['success' => false, 'message' => 'Reviews module is not set up yet. Please run: php artisan migrate'], 422);
        }
        $request->validate([
            'inquiry_ids' => 'required|array',
            'inquiry_ids.*' => 'integer',
            'assigned_to' => 'nullable',
        ]);

        $assignTo = $request->input('assigned_to');
        $assignTo = ($assignTo === null || $assignTo === '' || $assignTo === 'auto') ? null : (int) $assignTo;
        if ($assignTo && !User::whereKey($assignTo)->exists()) {
            return response()->json(['success' => false, 'message' => 'Selected user not found.'], 422);
        }

        $result = $this->reviews->syncMany($request->input('inquiry_ids'), $assignTo, Auth::id());

        $msg = $result['created'] . ' inquiry(s) synced to Reviews.';
        $skipped = [];
        if ($result['already']) {
            $skipped[] = $result['already'] . ' already in Reviews';
        }
        if ($result['not_completed']) {
            $skipped[] = $result['not_completed'] . ' not Completed';
        }
        if ($skipped) {
            $msg .= ' Skipped: ' . implode(', ', $skipped) . '.';
        }

        return response()->json(['success' => true, 'message' => $msg] + $result);
    }

    /* ------------------------------------------------------------------
     |  Follow-ups, history, reassign
     * ------------------------------------------------------------------ */

    public function followup(Request $request, $id)
    {
        $review = $this->findAccessible($id);

        $closing = in_array($request->input('outcome'), ['received', 'declined'], true);
        $data = $request->validate([
            'outcome' => 'required|in:' . implode(',', array_keys(InquiryReviewFollowup::OUTCOMES)),
            'channel' => 'nullable|in:' . implode(',', InquiryReviewFollowup::CHANNELS),
            'rating' => 'nullable|integer|min:1|max:5',
            'platforms' => 'nullable|array',
            'platforms.*' => 'in:' . implode(',', InquiryReview::PLATFORMS),
            'review_link' => 'nullable|string|max:500',
            'customer_comment' => 'nullable|string|max:2000',
            'remarks' => 'nullable|string|max:2000',
            'next_followup_date' => ($closing || $request->input('outcome') === 'note' ? 'nullable' : 'required') . '|date',
        ], [
            'next_followup_date.required' => 'Please choose the next follow-up date.',
        ]);

        $this->reviews->logFollowup($review, $data, Auth::id());

        return response()->json(['success' => true, 'message' => 'Follow-up saved.']);
    }

    public function history($id)
    {
        $review = $this->findAccessible($id);
        $review->load(['followups.user', 'assignee', 'customer']);

        return response()->json([
            'success' => true,
            'review' => [
                'status' => $review->status_label,
                'assignee' => optional($review->assignee)->name,
                'source' => $review->source,
                'completed_at' => optional($review->completed_at)->format('d M Y'),
                'due_date' => optional($review->due_date)->format('d M Y'),
                'rating' => $review->rating,
                'platforms' => $review->platforms ?: [],
                'review_link' => $review->review_link,
                'customer_comment' => $review->customer_comment,
            ],
            'followups' => $review->followups->map(fn ($f) => [
                'date' => $f->created_at ? $f->created_at->format('d M Y h:i A') : '',
                'user' => optional($f->user)->name ?? '—',
                'channel' => $f->channel,
                'outcome' => $f->outcome_label,
                'rating' => $f->rating,
                'platforms' => $f->platforms ?: [],
                'review_link' => $f->review_link,
                'remarks' => $f->remarks,
                'next' => optional($f->next_followup_date)->format('d M Y'),
            ])->values(),
        ]);
    }

    public function reassign(Request $request)
    {
        $request->validate([
            'review_ids' => 'required|array',
            'review_ids.*' => 'integer',
            'assigned_to' => 'required',
        ]);

        $to = $request->input('assigned_to') === 'none' ? null : (int) $request->input('assigned_to');
        if ($to && !User::whereKey($to)->exists()) {
            return response()->json(['success' => false, 'message' => 'Selected user not found.'], 422);
        }

        $count = $this->reviews->reassign($request->input('review_ids'), $to, Auth::id());

        return response()->json(['success' => true, 'message' => $count . ' review(s) reassigned.']);
    }

    /* ------------------------------------------------------------------
     |  Helpers
     * ------------------------------------------------------------------ */

    private function canSeeAll(): bool
    {
        return (bool) Auth::user()?->can('Reviews all');
    }

    private function applyScope($query): void
    {
        if (!$this->canSeeAll()) {
            $query->where('inquiry_reviews.assigned_to', Auth::id());
        }
    }

    private function findAccessible($id): InquiryReview
    {
        $review = InquiryReview::findOrFail((int) $id);
        if (!$this->canSeeAll() && (int) $review->assigned_to !== (int) Auth::id()) {
            abort(403, 'This review is not assigned to you.');
        }
        return $review;
    }

    private function buildCounts(): array
    {
        $today = Carbon::today()->toDateString();
        $open = InquiryReview::OPEN_STATUSES;
        $base = function () {
            $q = InquiryReview::query();
            $this->applyScope($q);
            return $q;
        };

        return [
            'open' => $base()->whereIn('status', $open)->count(),
            'due_today' => $base()->whereIn('status', $open)->whereDate('next_followup_date', $today)->count(),
            'overdue' => $base()->whereIn('status', $open)->whereDate('next_followup_date', '<', $today)->count(),
            'pending' => $base()->where('status', 'pending')->count(),
            'requested' => $base()->where('status', 'requested')->count(),
            'not_reachable' => $base()->where('status', 'not_reachable')->count(),
            'received' => $base()->where('status', 'received')->count(),
            'declined' => $base()->where('status', 'declined')->count(),
            'all' => $base()->count(),
        ];
    }

    private function statusBadge(?string $status): string
    {
        $label = InquiryReview::STATUSES[$status] ?? ucfirst((string) $status);
        return '<span class="rv-badge rv-' . e($status) . '">' . e($label) . '</span>';
    }

    /** Initials avatar with a stable colour per name. */
    private function avatar(string $name, bool $small = false): string
    {
        $words = preg_split('/\s+/', trim($name)) ?: [];
        $initials = strtoupper(mb_substr($words[0] ?? '?', 0, 1) . mb_substr($words[1] ?? '', 0, 1));
        $palette = ['#4f6bed', '#0f9d8a', '#c2410c', '#7c3aed', '#be185d', '#0369a1', '#4d7c0f', '#b45309'];
        $color = $palette[abs(crc32($name)) % count($palette)];
        return '<span class="rv-avatar' . ($small ? ' sm' : '') . '" style="background:' . $color . '">' . e($initials) . '</span>';
    }

    private function fmtDate($value): string
    {
        if (empty($value)) {
            return '—';
        }
        try {
            return Carbon::parse($value)->format('d M Y');
        } catch (\Throwable $e) {
            return e($value);
        }
    }

    private function companyName(): string
    {
        try {
            $name = OrganizationSetting::query()->value('name');
        } catch (\Throwable $e) {
            $name = null;
        }
        return $name ?: config('app.name', 'our team');
    }

    /** wa.me link with the review request message filled in. */
    private function whatsappUrl($r, SystemIntegrationSetting $settings, string $company): ?string
    {
        $digits = preg_replace('/\D/', '', (string) ($r->whatsapp_number ?: $r->customer_cell));
        if (strlen($digits) < 10) {
            return null;
        }
        if (str_starts_with($digits, '00')) {
            $digits = substr($digits, 2);
        } elseif (strlen($digits) === 11 && str_starts_with($digits, '0')) {
            $digits = '92' . substr($digits, 1);            // 03001234567 -> 923001234567
        } elseif (strlen($digits) === 10 && str_starts_with($digits, '3')) {
            $digits = '92' . $digits;                       // 3001234567 -> 923001234567
        }

        $links = is_array($settings->review_links) ? array_filter($settings->review_links) : [];
        $linkText = collect($links)->map(fn ($url, $platform) => ucfirst($platform) . ': ' . $url)->implode("\n");

        $template = $settings->review_whatsapp_template ?: self::defaultTemplate();
        $message = strtr($template, [
            '{customer_name}' => $r->customer_name ?: '',
            '{company}' => $company,
            '{inquiry_id}' => (string) $r->inquiry_id,
            '{review_links}' => $linkText,
        ]);

        return 'https://wa.me/' . $digits . '?text=' . rawurlencode(trim($message));
    }

    public static function defaultTemplate(): string
    {
        return "Assalam o Alaikum {customer_name},\n\nThank you for travelling with {company}. We hope you had a blessed and comfortable journey.\n\nWe would be very grateful if you could share your experience with us:\n{review_links}\n\nJazakAllah Khair!";
    }
}
