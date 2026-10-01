<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InquiryReviewFollowup extends Model
{
    protected $table = 'inquiry_review_followups';

    public const OUTCOMES = [
        'link_sent' => 'Review link sent',
        'will_do_later' => 'Will do later',
        'not_reachable' => 'Not reachable',
        'received' => 'Review received',
        'declined' => 'Declined',
        'note' => 'Note only',
    ];

    public const CHANNELS = ['WhatsApp', 'Call', 'SMS', 'Email', 'In Person'];

    protected $fillable = [
        'review_id', 'user_id', 'channel', 'outcome', 'rating', 'platforms',
        'review_link', 'remarks', 'next_followup_date',
    ];

    protected $casts = [
        'platforms' => 'array',
        'next_followup_date' => 'date',
    ];

    public function review()
    {
        return $this->belongsTo(InquiryReview::class, 'review_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getOutcomeLabelAttribute(): string
    {
        return self::OUTCOMES[$this->outcome] ?? ucfirst((string) $this->outcome);
    }
}
