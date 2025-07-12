<?php

namespace App\Console\Commands;

use App\Models\FatigueActivity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckFatigueDeadlines extends Command
{
    protected $signature = 'fatigue:check-deadlines';
    protected $description = 'Check fatigue activity deadlines and send WhatsApp notifications';

    public function handle()
    {
        $this->info('Starting fatigue deadline check...');
        Log::channel('notifications')->info('Starting fatigue deadline check');

        $activities = FatigueActivity::with(['user' => function($query) {
                $query->whereNotNull('no_telp');
            }])
            ->whereNull('result_path')
            ->get()
            ->filter(function($activity) {
                return $activity->should_send_notification;
            });

        if ($activities->isEmpty()) {
            $this->info('No notifications to send at this time.');
            Log::channel('notifications')->info('No notifications to send');
            return;
        }

        $this->info("Found {$activities->count()} activities requiring notifications");

        $successCount = 0;
        $failCount = 0;

        $activities->each(function ($activity) use (&$successCount, &$failCount) {
            try {
                $result = $activity->sendNotification();

                if ($result) {
                    $this->info("Sent to {$activity->user->no_telp} (Activity ID: {$activity->id})");
                    $successCount++;
                } else {
                    $this->error("Failed to send for Activity ID: {$activity->id}");
                    $failCount++;
                }
            } catch (\Exception $e) {
                $this->error("Error processing Activity ID {$activity->id}: " . $e->getMessage());
                Log::channel('notifications')->error("Error processing activity {$activity->id}", [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $failCount++;
            }
        });

        $summary = "Processed {$activities->count()} activities: {$successCount} succeeded, {$failCount} failed";
        $this->info($summary);
        Log::channel('notifications')->info($summary);
    }
}
