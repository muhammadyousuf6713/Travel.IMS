<?php

namespace App\Services;

use App\Models\SystemSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SystemPlanService
{
    /**
     * Retrieve the current system subscription plan status and settings.
     *
     * @return array
     */
    public static function getStatus(): array
    {
        $enabled = SystemSetting::get('system_plan_enabled', '0') === '1';
        $planType = SystemSetting::get('system_plan_type', 'monthly');
        $amount = SystemSetting::get('system_plan_amount', '');
        $currency = SystemSetting::get('system_plan_currency', 'PKR');
        $deadlineStr = SystemSetting::get('system_plan_deadline', '');
        $warningDays = (int) SystemSetting::get('system_plan_warning_days', 7);
        $graceDays = (int) SystemSetting::get('system_plan_grace_days', 0);

        $bannerTitle = SystemSetting::get('system_plan_banner_title', 'System Subscription Renewal Notice');
        $bannerMessage = SystemSetting::get(
            'system_plan_banner_message',
            'Attention: Scheduled system subscription renewal is due on :deadline (:time_left remaining). Please contact the system owner to process renewal and avoid service disruption.'
        );

        $suspendedTitle = SystemSetting::get('system_plan_suspended_title', 'System Access Temporarily Suspended');
        $suspendedMessage = SystemSetting::get(
            'system_plan_suspended_message',
            'This system service has reached its subscription renewal deadline and is currently placed on hold. All business records, customer files, and database data remain intact and secure. Please contact the System Owner to continue and restore immediate access.'
        );

        $ownerName = SystemSetting::get('system_plan_owner_name', 'System Administration');
        $ownerPhone = SystemSetting::get('system_plan_owner_phone', '');
        $ownerEmail = SystemSetting::get('system_plan_owner_email', '');
        $ownerWhatsapp = SystemSetting::get('system_plan_owner_whatsapp', '');

        $isConfigured = !empty($deadlineStr);
        $isWarning = false;
        $isSuspended = false;
        $daysRemaining = null;
        $hoursRemaining = null;
        $timeLeftText = '';
        $formattedDeadline = '';

        if ($enabled && $isConfigured) {
            try {
                $deadlineDate = Carbon::parse($deadlineStr)->startOfDay();
                $formattedDeadline = $deadlineDate->format('d M Y');

                // Hard lockout begins at the end of the deadline date + grace days
                $lockoutDate = Carbon::parse($deadlineStr)->addDays($graceDays)->endOfDay();
                $warningStartDate = Carbon::parse($deadlineStr)->subDays($warningDays)->startOfDay();

                $now = Carbon::now();

                if ($now->greaterThan($lockoutDate)) {
                    $isSuspended = true;
                } elseif ($now->greaterThanOrEqualTo($warningStartDate)) {
                    $isWarning = true;

                    $diffSec = $now->diffInSeconds($lockoutDate, false);
                    $daysRemaining = (int) floor($diffSec / 86400);
                    $hoursRemaining = (int) floor($diffSec / 3600);

                    if ($daysRemaining > 1) {
                        $timeLeftText = "{$daysRemaining} days";
                    } elseif ($daysRemaining === 1) {
                        $timeLeftText = "1 day (" . ($hoursRemaining % 24) . " hours)";
                    } else {
                        $timeLeftText = "{$hoursRemaining} hours (Due Today)";
                    }
                }
            } catch (\Exception $e) {
                // Ignore parse errors, treat as non-configured
                $isConfigured = false;
            }
        }

        // Interpolate banner message
        $parsedBannerMessage = str_replace(
            [':deadline', ':time_left', ':days_left', ':amount', ':currency'],
            [$formattedDeadline, $timeLeftText, $daysRemaining, $amount, $currency],
            $bannerMessage
        );

        return [
            'enabled' => $enabled,
            'plan_type' => $planType,
            'amount' => $amount,
            'currency' => $currency,
            'deadline' => $deadlineStr,
            'formatted_deadline' => $formattedDeadline,
            'warning_days' => $warningDays,
            'grace_days' => $graceDays,
            'banner_title' => $bannerTitle,
            'banner_message' => $bannerMessage,
            'parsed_banner_message' => $parsedBannerMessage,
            'suspended_title' => $suspendedTitle,
            'suspended_message' => $suspendedMessage,
            'owner_name' => $ownerName,
            'owner_phone' => $ownerPhone,
            'owner_email' => $ownerEmail,
            'owner_whatsapp' => $ownerWhatsapp,
            'is_configured' => $isConfigured,
            'is_warning' => $isWarning,
            'is_suspended' => $isSuspended,
            'days_remaining' => $daysRemaining,
            'hours_remaining' => $hoursRemaining,
            'time_left_text' => $timeLeftText,
        ];
    }

    /**
     * Save System Plan settings from request.
     *
     * @param Request $request
     * @return void
     */
    public static function saveSettings(Request $request): void
    {
        $enabled = $request->boolean('system_plan_enabled');
        SystemSetting::set('system_plan_enabled', $enabled ? '1' : '0');

        if ($request->has('system_plan_type')) {
            SystemSetting::set('system_plan_type', (string) $request->input('system_plan_type', 'monthly'));
        }
        if ($request->has('system_plan_amount')) {
            SystemSetting::set('system_plan_amount', (string) $request->input('system_plan_amount', ''));
        }
        if ($request->has('system_plan_currency')) {
            SystemSetting::set('system_plan_currency', (string) $request->input('system_plan_currency', 'PKR'));
        }
        if ($request->has('system_plan_deadline')) {
            SystemSetting::set('system_plan_deadline', (string) $request->input('system_plan_deadline', ''));
        }
        if ($request->has('system_plan_warning_days')) {
            SystemSetting::set('system_plan_warning_days', (string) max(1, (int) $request->input('system_plan_warning_days', 7)));
        }
        if ($request->has('system_plan_grace_days')) {
            SystemSetting::set('system_plan_grace_days', (string) max(0, (int) $request->input('system_plan_grace_days', 0)));
        }
        if ($request->has('system_plan_banner_title')) {
            SystemSetting::set('system_plan_banner_title', (string) $request->input('system_plan_banner_title'));
        }
        if ($request->has('system_plan_banner_message')) {
            SystemSetting::set('system_plan_banner_message', (string) $request->input('system_plan_banner_message'));
        }
        if ($request->has('system_plan_suspended_title')) {
            SystemSetting::set('system_plan_suspended_title', (string) $request->input('system_plan_suspended_title'));
        }
        if ($request->has('system_plan_suspended_message')) {
            SystemSetting::set('system_plan_suspended_message', (string) $request->input('system_plan_suspended_message'));
        }
        if ($request->has('system_plan_owner_name')) {
            SystemSetting::set('system_plan_owner_name', (string) $request->input('system_plan_owner_name'));
        }
        if ($request->has('system_plan_owner_phone')) {
            SystemSetting::set('system_plan_owner_phone', (string) $request->input('system_plan_owner_phone'));
        }
        if ($request->has('system_plan_owner_email')) {
            SystemSetting::set('system_plan_owner_email', (string) $request->input('system_plan_owner_email'));
        }
        if ($request->has('system_plan_owner_whatsapp')) {
            SystemSetting::set('system_plan_owner_whatsapp', (string) $request->input('system_plan_owner_whatsapp'));
        }
    }
}
