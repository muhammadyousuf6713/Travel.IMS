<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingFlight extends Model
{
    use HasFactory;

    protected $table = 'booking_flights';

    protected $fillable = [
        'booking_id',
        'flight_no',
        'airline',
        'from_city',
        'to_city',
        'etd',
        'eta',
        'pnr',
    ];

    protected $casts = [
        'etd' => 'datetime',
        'eta' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(PilgrimageBooking::class, 'booking_id');
    }
}
