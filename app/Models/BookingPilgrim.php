<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingPilgrim extends Model
{
    use HasFactory;

    protected $table = 'booking_pilgrims';

    protected $fillable = [
        'booking_id',
        'name',
        'passport_no',
        'gender',
        'age_type',
    ];

    public function booking()
    {
        return $this->belongsTo(PilgrimageBooking::class, 'booking_id');
    }
}
