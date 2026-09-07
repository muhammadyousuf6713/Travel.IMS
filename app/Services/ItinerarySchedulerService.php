<?php

namespace App\Services;

use App\Models\BookingFlight;
use App\Models\BookingHotel;
use App\Models\BookingPilgrim;
use App\Models\ItineraryLeg;
use App\Models\PilgrimageBooking;
use App\Models\PilgrimageSetting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ItinerarySchedulerService
{
    /**
     * Create a new booking and auto-schedule all legs.
     */
    public function createBooking(array $data, int $submittingAgentId): PilgrimageBooking
    {
        return DB::transaction(function () use ($data, $submittingAgentId) {
            $pilgrimsData = $data['pilgrims'] ?? [];
            $flightsData = $data['flights'] ?? [];
            $hotelsData = $data['hotels'] ?? [];
            $customLegsData = $data['custom_legs'] ?? [];

            $totalPersons = 1 + count($pilgrimsData);

            $booking = PilgrimageBooking::create([
                'crm_id' => trim($data['crm_id']),
                'service_type' => $data['service_type'] ?? 'umrah',
                'submitting_agent_id' => $submittingAgentId,
                'leader_name' => trim($data['leader_name']),
                'leader_passport_no' => !empty($data['leader_passport_no']) ? trim($data['leader_passport_no']) : null,
                'leader_contact_no' => !empty($data['leader_contact_no']) ? trim($data['leader_contact_no']) : null,
                'total_persons' => $totalPersons,
                'notes' => $data['notes'] ?? null,
                'overall_status' => 'active',
                'submitted_at' => Carbon::now(),
            ]);

            // 1. Save Pilgrims
            foreach ($pilgrimsData as $pilgrim) {
                if (!empty($pilgrim['name'])) {
                    $booking->pilgrims()->create([
                        'name' => trim($pilgrim['name']),
                        'passport_no' => !empty($pilgrim['passport_no']) ? trim($pilgrim['passport_no']) : null,
                        'gender' => $pilgrim['gender'] ?? null,
                        'age_type' => $pilgrim['age_type'] ?? 'adult',
                    ]);
                }
            }

            // 2. Save Flights
            foreach ($flightsData as $flight) {
                if (!empty($flight['flight_no']) && !empty($flight['etd'])) {
                    $booking->flights()->create([
                        'flight_no' => trim($flight['flight_no']),
                        'airline' => $flight['airline'] ?? null,
                        'from_city' => trim($flight['from_city']),
                        'to_city' => trim($flight['to_city']),
                        'etd' => Carbon::parse($flight['etd']),
                        'eta' => !empty($flight['eta']) ? Carbon::parse($flight['eta']) : null,
                        'pnr' => !empty($flight['pnr']) ? trim($flight['pnr']) : null,
                    ]);
                }
            }

            // 3. Save Hotels
            foreach ($hotelsData as $hotel) {
                if (!empty($hotel['hotel_name']) && !empty($hotel['city']) && !empty($hotel['check_in'])) {
                    $booking->hotels()->create([
                        'city' => trim($hotel['city']),
                        'hotel_name' => trim($hotel['hotel_name']),
                        'voucher_no' => !empty($hotel['voucher_no']) ? trim($hotel['voucher_no']) : null,
                        'room_qty' => !empty($hotel['room_qty']) ? (int) $hotel['room_qty'] : 1,
                        'room_type' => $hotel['room_type'] ?? 'Double',
                        'check_in' => Carbon::parse($hotel['check_in']),
                        'check_out' => Carbon::parse($hotel['check_out']),
                        'schedule_ziyaraat' => isset($hotel['schedule_ziyaraat']) ? (bool) $hotel['schedule_ziyaraat'] : true,
                    ]);
                }
            }

            // 4. Generate All Itinerary Legs
            $this->generateItineraryLegs($booking, $customLegsData);

            return $booking;
        });
    }

    /**
     * Update an existing booking and regenerate/resync itinerary legs.
     */
    public function updateBooking(PilgrimageBooking $booking, array $data): PilgrimageBooking
    {
        return DB::transaction(function () use ($booking, $data) {
            $pilgrimsData = $data['pilgrims'] ?? [];
            $flightsData = $data['flights'] ?? [];
            $hotelsData = $data['hotels'] ?? [];
            $customLegsData = $data['custom_legs'] ?? [];

            $totalPersons = 1 + count($pilgrimsData);

            $booking->update([
                'crm_id' => trim($data['crm_id']),
                'service_type' => $data['service_type'] ?? $booking->service_type,
                'leader_name' => trim($data['leader_name']),
                'leader_passport_no' => !empty($data['leader_passport_no']) ? trim($data['leader_passport_no']) : null,
                'leader_contact_no' => !empty($data['leader_contact_no']) ? trim($data['leader_contact_no']) : null,
                'total_persons' => $totalPersons,
                'notes' => $data['notes'] ?? null,
            ]);

            // Sync Pilgrims
            $booking->pilgrims()->delete();
            foreach ($pilgrimsData as $pilgrim) {
                if (!empty($pilgrim['name'])) {
                    $booking->pilgrims()->create([
                        'name' => trim($pilgrim['name']),
                        'passport_no' => !empty($pilgrim['passport_no']) ? trim($pilgrim['passport_no']) : null,
                        'gender' => $pilgrim['gender'] ?? null,
                        'age_type' => $pilgrim['age_type'] ?? 'adult',
                    ]);
                }
            }

            // Sync Flights
            $booking->flights()->delete();
            foreach ($flightsData as $flight) {
                if (!empty($flight['flight_no']) && !empty($flight['etd'])) {
                    $booking->flights()->create([
                        'flight_no' => trim($flight['flight_no']),
                        'airline' => $flight['airline'] ?? null,
                        'from_city' => trim($flight['from_city']),
                        'to_city' => trim($flight['to_city']),
                        'etd' => Carbon::parse($flight['etd']),
                        'eta' => !empty($flight['eta']) ? Carbon::parse($flight['eta']) : null,
                        'pnr' => !empty($flight['pnr']) ? trim($flight['pnr']) : null,
                    ]);
                }
            }

            // Sync Hotels
            $booking->hotels()->delete();
            foreach ($hotelsData as $hotel) {
                if (!empty($hotel['hotel_name']) && !empty($hotel['city']) && !empty($hotel['check_in'])) {
                    $booking->hotels()->create([
                        'city' => trim($hotel['city']),
                        'hotel_name' => trim($hotel['hotel_name']),
                        'voucher_no' => !empty($hotel['voucher_no']) ? trim($hotel['voucher_no']) : null,
                        'room_qty' => !empty($hotel['room_qty']) ? (int) $hotel['room_qty'] : 1,
                        'room_type' => $hotel['room_type'] ?? 'Double',
                        'check_in' => Carbon::parse($hotel['check_in']),
                        'check_out' => Carbon::parse($hotel['check_out']),
                        'schedule_ziyaraat' => isset($hotel['schedule_ziyaraat']) ? (bool) $hotel['schedule_ziyaraat'] : true,
                    ]);
                }
            }

            // Preserve existing completed leg statuses if applicable
            $existingCompletedKeys = $booking->itineraryLegs()
                ->where('status', 'done')
                ->get()
                ->keyBy(function ($leg) {
                    return $leg->leg_type . '_' . $leg->city_or_route;
                });

            // Regenerate legs
            $booking->itineraryLegs()->delete();
            $this->generateItineraryLegs($booking, $customLegsData, $existingCompletedKeys);

            return $booking;
        });
    }

    /**
     * Generate all itinerary legs for a booking based on PRD business rules.
     */
    public function generateItineraryLegs(PilgrimageBooking $booking, array $customLegs = [], $existingCompleted = null): void
    {
        $settings = PilgrimageSetting::getSettings();
        $ziyaraatOffset = $settings->ziyaraat_offset_hours ?? 48;

        // 1. Flight Legs: Each flight creates 1 leg due at ETD
        $flights = $booking->flights()->orderBy('etd', 'asc')->get();
        foreach ($flights as $flight) {
            $route = "{$flight->from_city} → {$flight->to_city}";
            $key = 'flight_' . $route;
            $isDone = $existingCompleted && $existingCompleted->has($key);

            ItineraryLeg::create([
                'booking_id' => $booking->id,
                'leg_type' => 'flight',
                'title' => "Flight {$flight->flight_no}",
                'city_or_route' => $route,
                'event_datetime' => $flight->etd,
                'details' => "Flight: {$flight->flight_no} (" . ($flight->airline ?? 'Airlines') . ") | Route: {$route} | PNR: " . ($flight->pnr ?? 'N/A') . " | ETD: " . $flight->etd->format('d M Y H:i'),
                'status' => $isDone ? 'done' : 'pending',
                'completed_at' => $isDone ? Carbon::now() : null,
                'source_type' => BookingFlight::class,
                'source_id' => $flight->id,
            ]);
        }

        // 2. Hotel Legs & Smart Ziyaraat Auto-Scheduling
        $hotels = $booking->hotels()->orderBy('check_in', 'asc')->get();
        $scheduledZiyaraatCities = [];

        foreach ($hotels as $hotel) {
            $city = trim($hotel->city);

            // Hotel Check-in Leg (due at check_in)
            $checkinKey = 'hotel_checkin_' . $city;
            $isCheckinDone = $existingCompleted && $existingCompleted->has($checkinKey);

            ItineraryLeg::create([
                'booking_id' => $booking->id,
                'leg_type' => 'hotel_checkin',
                'title' => $hotel->hotel_name,
                'city_or_route' => $city,
                'event_datetime' => $hotel->check_in,
                'details' => "Check-in: {$hotel->hotel_name} ({$city}) | Room: {$hotel->room_qty}x {$hotel->room_type} | Ref: " . ($hotel->voucher_no ?? 'N/A') . " | Check-in: " . $hotel->check_in->format('d M Y H:i'),
                'status' => $isCheckinDone ? 'done' : 'pending',
                'completed_at' => $isCheckinDone ? Carbon::now() : null,
                'source_type' => BookingHotel::class,
                'source_id' => $hotel->id,
            ]);

            // Smart Ziyaraat Leg (Check-in + 48 hours, Deduplicated per city)
            $cityNormalized = strtolower($city);
            if ($hotel->schedule_ziyaraat && !in_array($cityNormalized, $scheduledZiyaraatCities)) {
                $scheduledZiyaraatCities[] = $cityNormalized;
                $ziyaraatDue = (clone $hotel->check_in)->addHours($ziyaraatOffset);
                $ziyaraatKey = 'ziyaraat_' . $city;
                $isZiyaraatDone = $existingCompleted && $existingCompleted->has($ziyaraatKey);

                ItineraryLeg::create([
                    'booking_id' => $booking->id,
                    'leg_type' => 'ziyaraat',
                    'title' => "Holy Sites & Ziyaraat",
                    'city_or_route' => $city,
                    'event_datetime' => $ziyaraatDue,
                    'details' => "Scheduled religious visits & historical landmarks in {$city}. Staff reminder to arrange transport/guide.",
                    'status' => $isZiyaraatDone ? 'done' : 'pending',
                    'completed_at' => $isZiyaraatDone ? Carbon::now() : null,
                    'source_type' => BookingHotel::class,
                    'source_id' => $hotel->id,
                ]);
            }

            // Hotel Check-out / Transfer Leg (due at check_out)
            $checkoutKey = 'hotel_checkout_' . $city;
            $isCheckoutDone = $existingCompleted && $existingCompleted->has($checkoutKey);

            ItineraryLeg::create([
                'booking_id' => $booking->id,
                'leg_type' => 'hotel_checkout',
                'title' => $hotel->hotel_name,
                'city_or_route' => $city,
                'event_datetime' => $hotel->check_out,
                'details' => "Check-out: {$hotel->hotel_name} ({$city}) | Hotel departure & onward transfer arrangement | Check-out: " . $hotel->check_out->format('d M Y H:i'),
                'status' => $isCheckoutDone ? 'done' : 'pending',
                'completed_at' => $isCheckoutDone ? Carbon::now() : null,
                'source_type' => BookingHotel::class,
                'source_id' => $hotel->id,
            ]);
        }

        // 3. Custom Extensibility Legs (Transfers, Tours, High-speed Trains)
        foreach ($customLegs as $custom) {
            if (!empty($custom['title']) && !empty($custom['event_datetime'])) {
                ItineraryLeg::create([
                    'booking_id' => $booking->id,
                    'leg_type' => $custom['leg_type'] ?? 'custom_transfer',
                    'title' => trim($custom['title']),
                    'city_or_route' => $custom['city_or_route'] ?? '',
                    'event_datetime' => Carbon::parse($custom['event_datetime']),
                    'details' => $custom['details'] ?? null,
                    'status' => 'pending',
                ]);
            }
        }
    }

    /**
     * Toggle Itinerary Leg status (Done <-> Pending).
     */
    public function toggleLegStatus(ItineraryLeg $leg, int $userId): array
    {
        if ($leg->status === 'pending') {
            $leg->status = 'done';
            $leg->completed_at = Carbon::now();
            $leg->completed_by = $userId;
        } else {
            $leg->status = 'pending';
            $leg->completed_at = null;
            $leg->completed_by = null;
        }

        $leg->save();

        // Refresh booking status
        $booking = $leg->booking;
        if ($booking->is_fully_completed) {
            $booking->overall_status = 'completed';
        } else {
            $booking->overall_status = 'active';
        }
        $booking->save();

        return [
            'success' => true,
            'leg_id' => $leg->id,
            'new_status' => $leg->status,
            'completed_at' => $leg->completed_at ? $leg->completed_at->format('d M Y, h:i A') : null,
            'badge' => $leg->status_badge,
            'dashboard_status' => $booking->dashboard_status,
            'is_fully_completed' => $booking->is_fully_completed,
        ];
    }
}
