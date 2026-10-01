<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\customer;
use App\followup_remark;
use App\inquiry;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Dashboard: read-only summary queries. Nothing here writes to the database.
 */
class DashboardController extends Controller
{
    /** Statuses that mean the deal is won */
    private const WON = ['Confirmed', 'Completed'];

    /** Statuses still being worked */
    private const ACTIVE = ['Open', 'In-Progress', 'Quotation', 'Hold'];

    public function index()
    {
        $today = Carbon::today();
        $monthStart = $today->copy()->startOfMonth();
        $lastMonthStart = $monthStart->copy()->subMonth();

        // ---------------------------------------------------------------
        // 1. Headline numbers
        // ---------------------------------------------------------------
        $totalInquiries = inquiry::count();
        $totalCustomers = customer::count();
        $totalUsers = User::count();

        // rolling 30-day windows (a calendar month looks empty on the 1st)
        $last30Start = $today->copy()->subDays(29);
        $prev30Start = $last30Start->copy()->subDays(30);
        $inquiriesThisMonth = inquiry::where('created_at', '>=', $last30Start)->count();
        $inquiriesLastMonth = inquiry::where('created_at', '>=', $prev30Start)->where('created_at', '<', $last30Start)->count();
        $inquiriesToday = inquiry::whereDate('created_at', $today)->count();
        $monthChange = $inquiriesLastMonth > 0
            ? round((($inquiriesThisMonth - $inquiriesLastMonth) / $inquiriesLastMonth) * 100)
            : null;

        $inquiryStatusCounts = inquiry::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $activePipeline = collect(self::ACTIVE)->sum(fn ($s) => (int) ($inquiryStatusCounts[$s] ?? 0));
        $wonTotal = collect(self::WON)->sum(fn ($s) => (int) ($inquiryStatusCounts[$s] ?? 0));
        $conversionRate = $totalInquiries > 0 ? round($wonTotal / $totalInquiries * 100, 1) : 0;
        $wonThisMonth = inquiry::whereIn('status', self::WON)->where('created_at', '>=', $last30Start)->count();

        $newCustomersThisMonth = customer::where('created_at', '>=', $last30Start)->count();

        // ---------------------------------------------------------------
        // 2. Follow-ups: based on each inquiry's LATEST follow-up
        //    (same rule as the Inquiry list's "FUD" filter, so the
        //    numbers match what you see when you click through)
        // ---------------------------------------------------------------
        $latestFollowups = DB::table('followup_remarks as f')
            ->joinSub(
                DB::table('followup_remarks')->selectRaw('MAX(id_followup_remarks) as id')->groupBy('inquiry_id'),
                'latest',
                'latest.id',
                '=',
                'f.id_followup_remarks'
            )
            ->join('inquiry as i', 'i.id_inquiry', '=', 'f.inquiry_id')
            ->whereNull('i.deleted_at');

        $todaysFollowupsCount = (clone $latestFollowups)->whereDate('f.followup_date', $today)->count();
        $overdueFollowupsCount = (clone $latestFollowups)->whereDate('f.followup_date', '<', $today)->count();
        $upcomingFollowupsCount = (clone $latestFollowups)->whereDate('f.followup_date', '>', $today)->count();

        $agenda = (clone $latestFollowups)
            ->leftJoin('customers as c', 'c.id_customers', '=', 'i.customer_id')
            ->leftJoin('users as u', 'u.id', '=', 'i.saleperson')
            ->whereDate('f.followup_date', '<=', $today)
            ->whereIn('i.status', self::ACTIVE)
            ->orderByDesc('f.followup_date')
            ->limit(8)
            ->get([
                'i.id_inquiry', 'i.status', 'c.customer_name', 'c.customer_cell',
                'u.name as salesperson', 'f.followup_date', 'f.remarks',
            ]);

        // ---------------------------------------------------------------
        // 3. 12-month trend: new inquiries vs won
        // ---------------------------------------------------------------
        $trendStart = $monthStart->copy()->subMonths(11);
        $monthly = inquiry::where('created_at', '>=', $trendStart)
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as ym, COUNT(*) as total, SUM(CASE WHEN status IN ('Confirmed','Completed') THEN 1 ELSE 0 END) as won")
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');

        $trend = ['labels' => [], 'total' => [], 'won' => []];
        for ($m = $trendStart->copy(); $m <= $monthStart; $m->addMonth()) {
            $key = $m->format('Y-m');
            $trend['labels'][] = $m->format('M y');
            $trend['total'][] = (int) ($monthly[$key]->total ?? 0);
            $trend['won'][] = (int) ($monthly[$key]->won ?? 0);
        }

        // ---------------------------------------------------------------
        // 4. Lead sources and inquiry types (top 6)
        // ---------------------------------------------------------------
        $leadSources = inquiry::leftJoin('sales_reference as sr', 'sr.type_id', '=', 'inquiry.sales_reference')
            ->selectRaw("COALESCE(NULLIF(TRIM(sr.type_name), ''), 'Not set') as name, COUNT(*) as total")
            ->groupBy('name')
            ->orderByDesc('total')
            ->limit(6)
            ->get();

        // ---------------------------------------------------------------
        // 5. Team leaderboard
        // ---------------------------------------------------------------
        $team = User::join('inquiry', 'users.id', '=', 'inquiry.saleperson')
            ->whereNull('inquiry.deleted_at')
            ->selectRaw("users.id, users.name, COUNT(*) as total,
                SUM(CASE WHEN inquiry.status IN ('Confirmed','Completed') THEN 1 ELSE 0 END) as won,
                SUM(CASE WHEN inquiry.status IN ('Open','In-Progress','Quotation','Hold') THEN 1 ELSE 0 END) as active")
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        // kept for compatibility with anything that still reads it
        $userPerformance = $team->map(fn ($u) => (object) ['id' => $u->id, 'name' => $u->name, 'inquiry_count' => $u->total]);

        // ---------------------------------------------------------------
        // 6. Reviews module (only if it has been set up)
        // ---------------------------------------------------------------
        $reviews = null;
        try {
            if (class_exists(\App\Services\ReviewService::class) && \App\Services\ReviewService::isReady()) {
                $open = ['pending', 'requested', 'not_reachable'];
                $reviews = [
                    'open' => DB::table('inquiry_reviews')->whereNull('deleted_at')->whereIn('status', $open)->count(),
                    'due' => DB::table('inquiry_reviews')->whereNull('deleted_at')->whereIn('status', $open)->whereDate('next_followup_date', '<=', $today)->count(),
                    'received_month' => DB::table('inquiry_reviews')->whereNull('deleted_at')->where('status', 'received')->where('received_at', '>=', $monthStart)->count(),
                    'avg_rating' => round((float) DB::table('inquiry_reviews')->whereNull('deleted_at')->where('status', 'received')->whereNotNull('rating')->avg('rating'), 1),
                ];
            }
        } catch (\Throwable $e) {
            $reviews = null;
        }

        // ---------------------------------------------------------------
        // 7. Inquiry status by salesperson (existing table, unchanged logic)
        // ---------------------------------------------------------------
        $rawCounts = inquiry::join('users', 'inquiry.saleperson', '=', 'users.id')
            ->select('users.id as salesperson_id', 'users.name as salesperson_name', 'inquiry.status', DB::raw('count(*) as total'))
            ->groupBy('users.id', 'users.name', 'inquiry.status')
            ->get();

        $salespersonStatusCounts = [];
        $allStatuses = ['Open', 'In-Progress', 'Quotation', 'Confirmed', 'Hold', 'Cancelled'];

        foreach ($rawCounts as $row) {
            $sp = $row->salesperson_name;
            if (!isset($salespersonStatusCounts[$sp])) {
                $salespersonStatusCounts[$sp] = [
                    'id' => $row->salesperson_id,
                    'counts' => array_fill_keys($allStatuses, 0),
                ];
            }
            $salespersonStatusCounts[$sp]['counts'][$row->status] = $row->total;
            if (!in_array($row->status, $allStatuses)) {
                $allStatuses[] = $row->status;
            }
        }
        // make sure every row has every column
        foreach ($salespersonStatusCounts as $sp => $data) {
            foreach ($allStatuses as $st) {
                $salespersonStatusCounts[$sp]['counts'][$st] = $data['counts'][$st] ?? 0;
            }
        }

        $userName = Auth::user()->name ?? 'there';
        $hour = (int) now()->format('G');
        $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');

        return view('dashboard', compact(
            'greeting', 'userName',
            'totalInquiries', 'totalCustomers', 'totalUsers',
            'inquiriesThisMonth', 'inquiriesLastMonth', 'inquiriesToday', 'monthChange',
            'inquiryStatusCounts', 'activePipeline', 'wonTotal', 'wonThisMonth', 'conversionRate',
            'newCustomersThisMonth',
            'todaysFollowupsCount', 'overdueFollowupsCount', 'upcomingFollowupsCount', 'agenda',
            'trend', 'leadSources', 'team', 'userPerformance', 'reviews',
            'salespersonStatusCounts', 'allStatuses'
        ));
    }
}
