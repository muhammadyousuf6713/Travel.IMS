<?php

namespace App\Http\Controllers;

use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HotelController extends Controller
{
    /**
     * Display a listing of hotels.
     */
    public function index(Request $request)
    {
        $query = Hotel::query();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('city', 'LIKE', "%{$search}%")
                  ->orWhere('distance', 'LIKE', "%{$search}%")
                  ->orWhere('address', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('star_rating')) {
            $query->where('star_rating', $request->star_rating);
        }

        $hotels = $query->orderBy('city', 'asc')->orderBy('name', 'asc')->paginate(20)->withQueryString();

        $cities = Hotel::select('city')->distinct()->orderBy('city', 'asc')->pluck('city');
        $ratings = ['5 Star', '4 Star', '3 Star', '2 Star', 'Standard / Economy'];

        return view('hotels.index', compact('hotels', 'cities', 'ratings'));
    }

    /**
     * Show the form for creating a new hotel.
     */
    public function create()
    {
        $ratings = ['5 Star', '4 Star', '3 Star', '2 Star', 'Standard / Economy'];
        $popularCities = ['Makkah', 'Madinah', 'Jeddah', 'Riyadh', 'Taif', 'Dammam', 'Dubai', 'Karachi', 'Lahore', 'Islamabad'];

        return view('hotels.create', compact('ratings', 'popularCities'));
    }

    /**
     * Store a newly created hotel in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'city' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'star_rating' => 'nullable|string|max:50',
            'distance' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'notes' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $validated['country'] = $validated['country'] ?? 'Saudi Arabia';
        $validated['status'] = $request->has('status') ? (bool) $request->status : true;

        $hotel = Hotel::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hotel registered successfully!',
                'hotel' => $hotel
            ]);
        }

        return redirect()->route('hotels.index')->with('success', "Hotel '{$hotel->name}' added successfully!");
    }

    /**
     * Show the form for editing the specified hotel.
     */
    public function edit(Hotel $hotel)
    {
        $ratings = ['5 Star', '4 Star', '3 Star', '2 Star', 'Standard / Economy'];
        $popularCities = ['Makkah', 'Madinah', 'Jeddah', 'Riyadh', 'Taif', 'Dammam', 'Dubai', 'Karachi', 'Lahore', 'Islamabad'];

        return view('hotels.edit', compact('hotel', 'ratings', 'popularCities'));
    }

    /**
     * Update the specified hotel in storage.
     */
    public function update(Request $request, Hotel $hotel)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'city' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'star_rating' => 'nullable|string|max:50',
            'distance' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'notes' => 'nullable|string',
            'status' => 'nullable|boolean',
        ]);

        $validated['country'] = $validated['country'] ?? 'Saudi Arabia';
        $validated['status'] = $request->has('status') ? (bool) $request->status : false;

        $hotel->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Hotel updated successfully!',
                'hotel' => $hotel
            ]);
        }

        return redirect()->route('hotels.index')->with('success', "Hotel '{$hotel->name}' updated successfully!");
    }

    /**
     * Remove the specified hotel from storage.
     */
    public function destroy(Hotel $hotel)
    {
        $name = $hotel->name;
        $hotel->delete();

        return redirect()->route('hotels.index')->with('success', "Hotel '{$name}' removed successfully.");
    }

    /**
     * AJAX: Get hotels list filtered by city
     */
    public function getByCity(Request $request)
    {
        $city = trim($request->input('city', ''));

        $query = Hotel::active();

        if (!empty($city)) {
            // Handle aliases like Makkah / Mecca or Madinah / Madina
            if (stripos($city, 'makk') !== false || stripos($city, 'mecc') !== false) {
                $query->where(function ($q) {
                    $q->where('city', 'LIKE', '%Makkah%')
                      ->orWhere('city', 'LIKE', '%Mecca%');
                });
            } elseif (stripos($city, 'madi') !== false) {
                $query->where(function ($q) {
                    $q->where('city', 'LIKE', '%Madinah%')
                      ->orWhere('city', 'LIKE', '%Madina%');
                });
            } else {
                $query->where('city', 'LIKE', "%{$city}%");
            }
        }

        $hotels = $query->orderBy('name', 'asc')->get(['id', 'name', 'city', 'star_rating', 'distance']);

        return response()->json([
            'success' => true,
            'hotels' => $hotels
        ]);
    }

    /**
     * AJAX: Quick store hotel from booking form
     */
    public function quickStore(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191',
            'city' => 'required|string|max:100',
            'country' => 'nullable|string|max:100',
            'star_rating' => 'nullable|string|max:50',
            'distance' => 'nullable|string|max:191',
            'address' => 'nullable|string',
            'contact_number' => 'nullable|string|max:50',
        ]);

        $validated['country'] = $validated['country'] ?? 'Saudi Arabia';
        $validated['status'] = true;

        $hotel = Hotel::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Hotel registered instantly!',
            'hotel' => $hotel
        ]);
    }
}
