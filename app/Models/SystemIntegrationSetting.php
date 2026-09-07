<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\User;
use App\sales_reference;
use App\inquirytypes;

class SystemIntegrationSetting extends Model
{
    use HasFactory;

    protected $table = 'system_integration_settings';

    protected $fillable = [
        'auto_assign_enabled',
        'assigned_user_ids',
        'last_assigned_user_index',
        'default_sales_reference_id',
        'default_inquiry_type_id',
        'default_inquiry_status',
        'api_key',
        'meta_verify_token',
        'meta_page_access_token',
        'meta_app_id',
        'meta_app_secret',
        'public_form_enabled',
        'public_form_title',
        'public_form_subtitle',
        'total_leads_processed',
        'last_lead_at',
    ];

    protected $casts = [
        'auto_assign_enabled' => 'boolean',
        'public_form_enabled' => 'boolean',
        'assigned_user_ids' => 'array',
        'last_lead_at' => 'datetime',
    ];

    /**
     * Singleton instance retriever
     */
    public static function getSettings(): self
    {
        $setting = self::first();
        if (!$setting) {
            // Find default sales reference (e.g. Facebook)
            $fbRef = sales_reference::where('type_name', 'LIKE', '%Facebook%')->first();
            $defaultRefId = $fbRef ? $fbRef->type_id : 13;

            // Find default inquiry type (e.g. Umrah Package or first type)
            $umrahType = inquirytypes::where('type_name', 'LIKE', '%Umrah Package%')->first();
            $defaultTypeId = $umrahType ? $umrahType->type_id : 61;

            // Get default sales team users (e.g., Ukasha, Shakaib, etc.)
            $defaultSalesUsers = User::whereIn('role_id', [4, 5, 8])->pluck('id')->toArray();
            if (empty($defaultSalesUsers)) {
                $defaultSalesUsers = User::pluck('id')->take(5)->toArray();
            }

            $setting = self::create([
                'auto_assign_enabled' => true,
                'assigned_user_ids' => $defaultSalesUsers,
                'last_assigned_user_index' => -1,
                'default_sales_reference_id' => $defaultRefId,
                'default_inquiry_type_id' => $defaultTypeId,
                'default_inquiry_status' => 'Open',
                'api_key' => Str::random(48),
                'meta_verify_token' => 'travel_ims_' . Str::random(16),
                'public_form_enabled' => true,
                'public_form_title' => 'Book Your Tour & Travel Package',
                'public_form_subtitle' => 'Fill out your requirements below and our travel consultants will connect with you immediately.',
                'total_leads_processed' => 0,
            ]);
        }
        return $setting;
    }

    /**
     * Get the next sales agent in the Round-Robin sequence
     */
    public function getNextAssignedUser(): ?User
    {
        if (!$this->auto_assign_enabled) {
            return User::first();
        }

        $userIds = $this->assigned_user_ids;
        if (empty($userIds) || !is_array($userIds)) {
            // Fallback to all sales users
            $userIds = User::whereIn('role_id', [4, 5, 8])->pluck('id')->toArray();
            if (empty($userIds)) {
                $userIds = User::pluck('id')->toArray();
            }
        }

        $count = count($userIds);
        if ($count === 0) {
            return User::first();
        }

        $nextIndex = ($this->last_assigned_user_index + 1) % $count;
        $targetUserId = $userIds[$nextIndex];

        $user = User::find($targetUserId);

        // If user not found, try to find the next available user in the pool
        $attempts = 0;
        while (!$user && $attempts < $count) {
            $nextIndex = ($nextIndex + 1) % $count;
            $targetUserId = $userIds[$nextIndex];
            $user = User::find($targetUserId);
            $attempts++;
        }

        // Save new pointer and track analytics
        $this->last_assigned_user_index = $nextIndex;
        $this->total_leads_processed = ($this->total_leads_processed ?? 0) + 1;
        $this->last_lead_at = now();
        $this->save();

        return $user ?: User::first();
    }
}
