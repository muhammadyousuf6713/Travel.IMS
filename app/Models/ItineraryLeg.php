<?php

namespace App\Models;

use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItineraryLeg extends Model
{
    use HasFactory;

    protected $table = 'itinerary_legs';

    protected $fillable = [
        'booking_id',
        'leg_type',
        'title',
        'city_or_route',
        'event_datetime',
        'details',
        'status',
        'completed_at',
        'completed_by',
        'last_notified_at',
        'notification_count',
        'source_type',
        'source_id',
        'sort_order',
    ];

    protected $casts = [
        'event_datetime' => 'datetime',
        'completed_at' => 'datetime',
        'last_notified_at' => 'datetime',
        'notification_count' => 'integer',
        'sort_order' => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(PilgrimageBooking::class, 'booking_id');
    }

    public function completedByUser()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    /**
     * Plain-language status phrase for this leg based on Appendix A.
     */
    public function getComputedStatusPhraseAttribute(): string
    {
        switch ($this->leg_type) {
            case 'flight':
                return "Arrived: {$this->city_or_route}";

            case 'hotel_checkin':
                return "Checked in at {$this->title}, {$this->city_or_route}";

            case 'hotel_checkout':
                return "Checked out of {$this->title}, {$this->city_or_route}";

            case 'ziyaraat':
                return "Ziyaraat completed in {$this->city_or_route}";

            case 'custom_transfer':
            case 'custom_activity':
            default:
                return "{$this->title} completed";
        }
    }

    /**
     * Check if this leg is overdue (past event_datetime and still pending).
     */
    public function getIsOverdueAttribute(): bool
    {
        if ($this->status === 'done') {
            return false;
        }

        return $this->event_datetime && Carbon::now()->greaterThan($this->event_datetime);
    }

    /**
     * Check if this leg is due soon (within 48 hours and pending).
     */
    public function getIsDueSoonAttribute(): bool
    {
        if ($this->status === 'done') {
            return false;
        }

        $now = Carbon::now();
        $dueWindow = Carbon::now()->addHours(48);

        return $this->event_datetime && $this->event_datetime->between($now, $dueWindow);
    }

    /**
     * Human readable badge status.
     */
    public function getStatusBadgeAttribute(): array
    {
        if ($this->status === 'done') {
            return [
                'label' => 'Completed',
                'class' => 'bg-gradient-success text-white',
                'icon' => 'fa-circle-check',
            ];
        }

        if ($this->is_overdue) {
            return [
                'label' => 'Overdue',
                'class' => 'bg-gradient-danger text-white',
                'icon' => 'fa-triangle-exclamation',
            ];
        }

        if ($this->is_due_soon) {
            return [
                'label' => 'Due Soon',
                'class' => 'bg-gradient-warning text-white',
                'icon' => 'fa-clock',
            ];
        }

        return [
            'label' => 'Upcoming',
            'class' => 'bg-gradient-secondary text-white',
            'icon' => 'fa-calendar',
        ];
    }
}
