<?php

namespace App\Models;

use App\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'profile_picture',
        'password',
        'phone',
        'location',
        'about_me',
        'status',
        'role_id',
        'business_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get avatar URL if picture exists on disk.
     */
    public function getAvatarUrlAttribute()
    {
        if (!empty($this->profile_picture) && file_exists(public_path($this->profile_picture))) {
            return asset($this->profile_picture);
        }
        return null;
    }

    /**
     * User initials for fallback avatar badge.
     */
    public function getInitialsAttribute()
    {
        $words = preg_split('/\s+/', trim($this->name ?? 'User'));
        if (count($words) >= 2) {
            return strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }
        return strtoupper(mb_substr($this->name ?? 'U', 0, 1));
    }
}
