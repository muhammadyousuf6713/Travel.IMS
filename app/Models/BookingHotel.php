<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingHotel extends Model
{
    use HasFactory;

    protected $table = 'booking_hotels';

    protected $fillable = [
        'booking_id',
        'city',
        'hotel_name',
        'voucher_no',
        'room_qty',
        'room_type',
        'check_in',
        'check_out',
        'schedule_ziyaraat',
    ];

    protected $casts = [
        'check_in' => 'datetime',
        'check_out' => 'datetime',
        'schedule_ziyaraat' => 'boolean',
        'room_qty' => 'integer',
    ];

    public function booking()
    {
        return $this->belongsTo(PilgrimageBooking::class, 'booking_id');
    }

    /**
     * Compute nights stay.
     */
    public function getNightsAttribute(): int
    {
        if (!$this->check_in || !$this->check_out) {
            return 0;
        }

        $checkIn = Carbon::parse($this->check_in)->startOfDay();
        $checkOut = Carbon::parse($this->check_out)->startOfDay();

        $diff = $checkIn->diffInDays($checkOut);
        return $diff > 0 ? $diff : 1;
    }
}
