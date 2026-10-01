<?php

namespace App\Models;

use App\customer;
use App\inquiry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InquiryReview extends Model
{
    use SoftDeletes;

    protected $table = 'inquiry_reviews';

    public const STATUSES = [
        'pending' => 'Pending',
        'requested' => 'Requested',
        'not_reachable' => 'Not Reachable',
        'received' => 'Received',
        'declined' => 'Declined',
    ];

    /** Statuses that still need follow-up */
    public const OPEN_STATUSES = ['pending', 'requested', 'not_reachable'];

    public const PLATFORMS = ['Google', 'Facebook', 'Instagram', 'Website', 'TripAdvisor', 'Other'];

    protected $fillable = [
        'inquiry_id', 'customer_id', 'assigned_to', 'assigned_by', 'source', 'status',
        'completed_at', 'due_date', 'next_followup_date', 'attempts', 'last_contacted_at',
        'rating', 'platforms', 'review_link', 'customer_comment', 'received_at', 'note', 'created_by',
    ];

    protected $casts = [
        'platforms' => 'array',
        'completed_at' => 'datetime',
        'due_date' => 'date',
        'next_followup_date' => 'date',
        'last_contacted_at' => 'datetime',
        'received_at' => 'datetime',
    ];

    public function inquiry()
    {
        return $this->belongsTo(inquiry::class, 'inquiry_id', 'id_inquiry');
    }

    public function customer()
    {
        return $this->belongsTo(customer::class, 'customer_id', 'id_customers');
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function followups()
    {
        return $this->hasMany(InquiryReviewFollowup::class, 'review_id')->latest();
    }

    public function isOpen(): bool
    {
        return in_array($this->status, self::OPEN_STATUSES, true);
    }

    public function isOverdue(): bool
    {
        return $this->isOpen() && $this->next_followup_date && $this->next_followup_date->lt(Carbon::today());
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst((string) $this->status);
    }
}
