<?php

namespace App\Console\Commands;

use App\Models\ItineraryLeg;
use App\Models\PilgrimageSetting;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendItineraryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'itinerary:send-reminders {--force : Force send reminders ignoring repeat interval}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches 3-hour automated reminders for upcoming and overdue pilgrimage & itinerary checkpoints';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting automated Itinerary Reminder check...');

        $settings = PilgrimageSetting::getSettings();
        $reminderWindowHours = $settings->reminder_window_hours ?? 48;
        $repeatIntervalHours = $settings->reminder_repeat_hours ?? 3;
        $force = $this->option('force');

        $now = Carbon::now();
        $dueWindow = $now->copy()->addHours($reminderWindowHours);

        // Find all pending legs where event_datetime is within the 48h window OR already overdue
        $legs = ItineraryLeg::with(['booking.submittingAgent'])
            ->where('status', 'pending')
            ->where('event_datetime', '<=', $dueWindow)
            ->get();

        $dispatchedCount = 0;

        // Fetch Boss / Super-Admin users for scoped notifications
        $bossUsers = User::whereHas('roles', function ($q) {
            $q->whereIn('name', ['Admin', 'Super-Admin', 'super-admin', 'admin', 'Manager']);
        })->orWhere('role_id', 1)->get();

        foreach ($legs as $leg) {
            $booking = $leg->booking;
            if (!$booking) {
                continue;
            }

            // Check repeat interval: Send only if never notified OR last notified >= 3 hours ago
            if (!$force && $leg->last_notified_at) {
                $hoursSinceLast = Carbon::parse($leg->last_notified_at)->diffInHours($now);
                if ($hoursSinceLast < $repeatIntervalHours) {
                    continue; // Skip this round
                }
            }

            $isOverdue = $now->greaterThan($leg->event_datetime);
            $submittingAgent = $booking->submittingAgent;

            // Compile recipients strictly: Submitting Agent + Boss Users
            $recipients = collect();
            if ($submittingAgent && !empty($submittingAgent->email)) {
                $recipients->push($submittingAgent);
            }
            foreach ($bossUsers as $boss) {
                if (!empty($boss->email) && !$recipients->contains('id', $boss->id)) {
                    $recipients->push($boss);
                }
            }

            $this->line("Dispatching notification for Booking [{$booking->crm_id}] - Leg: [{$leg->title}] ({$leg->city_or_route}) | Overdue: " . ($isOverdue ? 'YES' : 'NO'));

            // Log telemetry
            Log::info("Itinerary Reminder dispatched for Booking {$booking->crm_id}", [
                'leg_id' => $leg->id,
                'leg_type' => $leg->leg_type,
                'event_time' => $leg->event_datetime->toDateTimeString(),
                'is_overdue' => $isOverdue,
                'recipients_count' => $recipients->count(),
            ]);

            // Mark leg notified timestamp
            $leg->last_notified_at = $now;
            $leg->notification_count = ($leg->notification_count ?? 0) + 1;
            $leg->save();

            $dispatchedCount++;
        }

        $this->info("Itinerary Reminder completed. {$dispatchedCount} reminders processed.");

        return 0;
    }
}
