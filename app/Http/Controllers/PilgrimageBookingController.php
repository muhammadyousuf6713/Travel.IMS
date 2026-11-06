<?php

namespace App\Http\Controllers;

use App\cities;
use App\Models\ItineraryLeg;
use App\Models\PilgrimageBooking;
use App\User;
use App\Services\ItinerarySchedulerService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PilgrimageBookingController extends Controller
{
    protected ItinerarySchedulerService $schedulerService;

    public function __construct(ItinerarySchedulerService $schedulerService)
    {
        $this->middleware('auth');
        $this->schedulerService = $schedulerService;
    }

    /**
     * Display Operations Dashboard & Status Overview.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        $query = PilgrimageBooking::with(['submittingAgent', 'itineraryLegs', 'pilgrims', 'hotels', 'flights'])
            ->orderBy('created_at', 'desc');

        // Notification & Access Scoping: Regular agents only see their own submissions
        if (!$isBossOrAdmin) {
            $query->where('submitting_agent_id', $user->id);
        }

        // Handle Reset Filters
        if ($request->has('reset_filters')) {
            session()->forget([
                'pilgrimage_filter_search',
                'pilgrimage_filter_status',
                'pilgrimage_filter_service_type',
            ]);
            return redirect()->route('pilgrimage.index');
        }

        // Remember filters in session or restore when returning
        if ($request->has('search') || $request->has('status') || $request->has('service_type')) {
            session([
                'pilgrimage_filter_search' => $request->search,
                'pilgrimage_filter_status' => $request->status,
                'pilgrimage_filter_service_type' => $request->service_type,
            ]);
        } elseif (!$request->has('page')) {
            if ($request->search === null && session()->has('pilgrimage_filter_search') && session('pilgrimage_filter_search') !== null) {
                $request->merge(['search' => session('pilgrimage_filter_search')]);
            }
            if ($request->status === null && session()->has('pilgrimage_filter_status') && session('pilgrimage_filter_status') !== null) {
                $request->merge(['status' => session('pilgrimage_filter_status')]);
            }
            if ($request->service_type === null && session()->has('pilgrimage_filter_service_type') && session('pilgrimage_filter_service_type') !== null) {
                $request->merge(['service_type' => session('pilgrimage_filter_service_type')]);
            }
        }

        // Search Filter (CRM ID, Leader Name, Passport No, or comma-separated CRM IDs e.g. 01, 02, 30)
        if ($request->filled('search')) {
            $search = trim($request->search);
            if (str_contains($search, ',')) {
                $rawTokens = preg_split('/[,]+/', $search);
                $crmTokens = [];
                foreach ($rawTokens as $tok) {
                    $c = trim($tok);
                    if ($c !== '') {
                        $crmTokens[] = $c;
                        $cleanedNum = ltrim($c, '#');
                        if (is_numeric($cleanedNum)) {
                            $crmTokens[] = (int) $cleanedNum;
                            $crmTokens[] = (string) $cleanedNum;
                        }
                    }
                }
                $crmTokens = array_values(array_unique($crmTokens));
                $query->where(function ($q) use ($crmTokens, $search) {
                    $q->whereIn('crm_id', $crmTokens);
                    foreach ($crmTokens as $ct) {
                        $q->orWhere('crm_id', 'like', "%{$ct}%");
                    }
                });
            } else {
                $query->where(function ($q) use ($search) {
                    $q->where('crm_id', 'like', "%{$search}%")
                      ->orWhere('leader_name', 'like', "%{$search}%")
                      ->orWhere('leader_passport_no', 'like', "%{$search}%")
                      ->orWhere('leader_contact_no', 'like', "%{$search}%");
                });
            }
        }

        // Status Filter
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'completed') {
                $query->where('overall_status', 'completed');
            } elseif ($request->status === 'active') {
                $query->where('overall_status', 'active');
            }
        }

        // Service Type Filter
        if ($request->filled('service_type') && $request->service_type !== 'all') {
            $query->where('service_type', $request->service_type);
        }

        $bookings = $query->paginate(15)->withQueryString();

        // Calculate Executive KPI Metrics
        $baseKpiQuery = PilgrimageBooking::query();
        if (!$isBossOrAdmin) {
            $baseKpiQuery->where('submitting_agent_id', $user->id);
        }

        $totalActive = (clone $baseKpiQuery)->where('overall_status', 'active')->count();
        $totalCompleted = (clone $baseKpiQuery)->where('overall_status', 'completed')->count();

        // Count pending legs due today or overdue
        $legQuery = ItineraryLeg::where('status', 'pending')
            ->whereHas('booking', function ($q) use ($isBossOrAdmin, $user) {
                if (!$isBossOrAdmin) {
                    $q->where('submitting_agent_id', $user->id);
                }
            });

        $now = Carbon::now();
        $overdueCount = (clone $legQuery)->where('event_datetime', '<', $now)->count();
        $dueTodayCount = (clone $legQuery)->whereBetween('event_datetime', [$now, $now->copy()->endOfDay()])->count();

        return view('pilgrimage.index', compact(
            'bookings',
            'totalActive',
            'totalCompleted',
            'overdueCount',
            'dueTodayCount',
            'isBossOrAdmin'
        ));
    }

    /**
     * Show the booking intake creation form.
     */
    public function create()
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        $agents = User::orderBy('name', 'asc')->get();
        $popularCities = ['Makkah', 'Madinah', 'Jeddah', 'Riyadh', 'Taif', 'Dammam', 'Dubai', 'Karachi', 'Lahore', 'Islamabad'];

        // Auto-generate suggested CRM reference
        $latestId = PilgrimageBooking::max('id') ?? 0;
        $suggestedCrmId = 'CRM-' . str_pad($latestId + 1001, 5, '0', STR_PAD_LEFT);

        $initialHotels = \App\Models\Hotel::active()->orderBy('name', 'asc')->get();
        return view('pilgrimage.create', compact('agents', 'popularCities', 'isBossOrAdmin', 'suggestedCrmId', 'initialHotels'));
    }

    /**
     * Store newly created booking and schedule legs.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        $request->validate([
            'crm_id' => 'required|string|max:50|unique:pilgrimage_bookings,crm_id',
            'leader_name' => 'required|string|max:191',
            'submitting_agent_id' => $isBossOrAdmin ? 'required|exists:users,id' : 'nullable',
            'service_type' => 'required|string',
        ]);

        // Validate at least one flight OR hotel entry is provided
        $hasFlights = !empty($request->flights) && !empty($request->flights[0]['flight_no']);
        $hasHotels = !empty($request->hotels) && !empty($request->hotels[0]['hotel_name']);
        $hasCustom = !empty($request->custom_legs) && !empty($request->custom_legs[0]['title']);

        if (!$hasFlights && !$hasHotels && !$hasCustom) {
            return back()->withInput()->with('error', 'Please provide at least one Flight or Hotel Accommodation entry.');
        }

        $submittingAgentId = $isBossOrAdmin && $request->filled('submitting_agent_id')
            ? (int) $request->submitting_agent_id
            : $user->id;

        try {
            $booking = $this->schedulerService->createBooking($request->all(), $submittingAgentId);

            return redirect()->route('pilgrimage.index')
                ->with('success', "Booking {$booking->crm_id} submitted successfully with " . $booking->itineraryLegs()->count() . " scheduled itinerary checkpoints!")
                ->with('generated_voucher_id', $booking->id);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating booking: ' . $e->getMessage());
        }
    }

    /**
     * Show booking edit view.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        $booking = PilgrimageBooking::with(['pilgrims', 'flights', 'hotels', 'itineraryLegs'])->findOrFail($id);

        if (!$isBossOrAdmin && $booking->submitting_agent_id !== $user->id) {
            abort(403, 'Unauthorized access to this booking record.');
        }

        $agents = User::orderBy('name', 'asc')->get();
        $popularCities = ['Makkah', 'Madinah', 'Jeddah', 'Riyadh', 'Taif', 'Dammam', 'Dubai', 'Karachi', 'Lahore', 'Islamabad'];

        $initialHotels = \App\Models\Hotel::active()->orderBy('name', 'asc')->get();
        return view('pilgrimage.edit', compact('booking', 'agents', 'popularCities', 'isBossOrAdmin', 'initialHotels'));
    }

    /**
     * Update existing booking.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        $booking = PilgrimageBooking::findOrFail($id);

        if (!$isBossOrAdmin && $booking->submitting_agent_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $request->validate([
            'crm_id' => 'required|string|max:50|unique:pilgrimage_bookings,crm_id,' . $booking->id,
            'leader_name' => 'required|string|max:191',
        ]);

        try {
            $this->schedulerService->updateBooking($booking, $request->all());

            return redirect()->route('pilgrimage.index')
                ->with('success', "Booking {$booking->crm_id} updated successfully.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating booking: ' . $e->getMessage());
        }
    }

    /**
     * Delete a booking.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        $booking = PilgrimageBooking::findOrFail($id);

        if (!$isBossOrAdmin && $booking->submitting_agent_id !== $user->id) {
            abort(403, 'Unauthorized access.');
        }

        $crmId = $booking->crm_id;
        $booking->delete();

        return redirect()->route('pilgrimage.index')
            ->with('success', "Booking {$crmId} removed successfully.");
    }

    /**
     * Fetch Booking Details for AJAX Drawer / Modal View.
     */
    public function getDetailModal($id)
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        $booking = PilgrimageBooking::with([
            'submittingAgent',
            'pilgrims',
            'flights',
            'hotels',
            'itineraryLegs.completedByUser'
        ])->findOrFail($id);

        if (!$isBossOrAdmin && $booking->submitting_agent_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $html = view('pilgrimage.partials.detail_modal', compact('booking'))->render();

        return response()->json([
            'success' => true,
            'html' => $html,
            'crm_id' => $booking->crm_id,
            'dashboard_status' => $booking->dashboard_status,
        ]);
    }

    /**
     * Toggle Itinerary Leg status via AJAX.
     */
    public function toggleLegStatus(Request $request, $legId)
    {
        $user = Auth::user();
        $isBossOrAdmin = $user->hasRole(['Admin', 'Super-Admin', 'Manager', 'super-admin', 'admin']) || ($user->role_id == 1);

        $leg = ItineraryLeg::with('booking')->findOrFail($legId);

        if (!$isBossOrAdmin && $leg->booking->submitting_agent_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $result = $this->schedulerService->toggleLegStatus($leg, $user->id);

        return response()->json($result);
    }
}
