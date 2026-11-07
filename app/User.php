<?php
namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'name', 'email', 'profile_picture', 'phone', 'location', 'about_me', 'password', 'status', 'business_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getAvatarUrlAttribute()
    {
        if (!empty($this->profile_picture) && file_exists(public_path($this->profile_picture))) {
            return asset($this->profile_picture);
        }
        return null;
    }

    public function getInitialsAttribute()
    {
        $words = preg_split('/\s+/', trim($this->name ?? 'User'));
        if (count($words) >= 2) {
            return strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        }
        return strtoupper(mb_substr($this->name ?? 'U', 0, 1));
    }
}
