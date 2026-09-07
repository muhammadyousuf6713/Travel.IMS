<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PilgrimageSetting extends Model
{
    use HasFactory;

    protected $table = 'pilgrimage_settings';

    protected $fillable = [
        'makkah_ops_contact',
        'madinah_ops_contact',
        'jeddah_ops_contact',
        'general_hotline',
        'ziyaraat_offset_hours',
        'reminder_window_hours',
        'reminder_repeat_hours',
    ];

    /**
     * Get singleton or default settings instance.
     */
    public static function getSettings(): self
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'makkah_ops_contact' => '+966 50 123 4567',
                'madinah_ops_contact' => '+966 50 765 4321',
                'jeddah_ops_contact' => '+966 50 999 8888',
                'general_hotline' => '+92 300 1234567',
                'ziyaraat_offset_hours' => 48,
                'reminder_window_hours' => 48,
                'reminder_repeat_hours' => 3,
            ]);
        }
        return $setting;
    }
}
