<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hotel extends Model
{
    use HasFactory;

    protected $table = 'hotels';

    protected $fillable = [
        'name',
        'city',
        'country',
        'star_rating',
        'distance',
        'address',
        'contact_number',
        'email',
        'notes',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Scope for active hotels
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for searching by city
     */
    public function scopeByCity($query, $city)
    {
        if (!empty($city)) {
            return $query->where('city', 'LIKE', '%' . trim($city) . '%');
        }
        return $query;
    }
}
