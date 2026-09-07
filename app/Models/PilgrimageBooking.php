<?php

namespace App\Models;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilgrimageBooking extends Model
{
    use HasFactory;

    protected $table = 'pilgrimage_bookings';

    protected $fillable = [
        'crm_id',
        'service_type',
        'submitting_agent_id',
        'leader_name',
        'leader_passport_no',
        'leader_contact_no',
        'total_persons',
        'notes',
        'overall_status',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'total_persons' => 'integer',
    ];

    /**
     * Model Boot Event to ensure complete cascade deletion of all child records.
     */
    protected static function booted()
    {
        static::deleting(function ($booking) {
            $booking->pilgrims()->delete();
            $booking->flights()->delete();
            $booking->hotels()->delete();
            $booking->itineraryLegs()->delete();
        });
    }

    public function submittingAgent()
    {
        return $this->belongsTo(User::class, 'submitting_agent_id');
    }

    public function pilgrims()
    {
        return $this->hasMany(BookingPilgrim::class, 'booking_id');
    }

    public function flights()
    {
        return $this->hasMany(BookingFlight::class, 'booking_id');
    }

    public function hotels()
    {
        return $this->hasMany(BookingHotel::class, 'booking_id');
    }

    public function itineraryLegs()
    {
        return $this->hasMany(ItineraryLeg::class, 'booking_id')->orderBy('event_datetime', 'asc')->orderBy('sort_order', 'asc');
    }

    /**
     * Compute plain-language status phrase for dashboard based on Appendix A rules.
     */
    public function getDashboardStatusAttribute(): string
    {
        // Find most recently completed ('done') leg based on itinerary chronology
        $lastCompletedLeg = $this->itineraryLegs()
            ->reorder()
            ->where('status', 'done')
            ->orderBy('event_datetime', 'desc')
            ->orderBy('completed_at', 'desc')
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastCompletedLeg) {
            return 'Not started yet';
        }

        return $lastCompletedLeg->computed_status_phrase;
    }

    /**
     * Get the next upcoming pending leg.
     */
    public function getNextDueLegAttribute()
    {
        return $this->itineraryLegs()
            ->reorder()
            ->where('status', 'pending')
            ->orderBy('event_datetime', 'asc')
            ->first();
    }

    /**
     * Check if all legs are completed.
     */
    public function getIsFullyCompletedAttribute(): bool
    {
        $totalLegs = $this->itineraryLegs()->count();
        if ($totalLegs === 0) {
            return false;
        }

        $pendingCount = $this->itineraryLegs()->where('status', 'pending')->count();
        return $pendingCount === 0;
    }
}
