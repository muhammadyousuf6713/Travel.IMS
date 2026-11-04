<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
        'favicon',
        'login_bg',
        'theme_color',
        'website',
        'email',
        'phone',
        'address',
        'city',
        'logo_height',
        'logo_width',
        'logo_padding',
        'logo_margin_top',
        'logo_margin_bottom'
    ];
}
