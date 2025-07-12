<?php

namespace App\Console\Commands;

use App\Models\FatigueActivity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendFatigueNotifications extends Command
{
    protected $signature = 'notifications:send-fatigue';
    protected $description = 'Send WhatsApp notifications for approaching fatigue activity deadlines';

    public function handle()
    {
        $activities = FatigueActivity::with('user')
            ->whereNull('result_path')
            ->get()
            ->filter(function ($activity) {
                return $activity->should_send_notification;
            });

        if ($activities->isEmpty()) {
            $this->info('No notifications to send at this time.');
            return;
        }

        $this->info("Sending notifications for {$activities->count()} activities...");

        $activities->each(function ($activity) {
            try {
                $result = $activity->sendNotification();

                if ($result) {
                    $this->line("Sent to {$activity->user->no_telp} (Activity ID: {$activity->id})");
                    Log::info("Notification sent for activity {$activity->id} to {$activity->user->no_telp}");
                } else {
                    $this->error("Failed to send for Activity ID: {$activity->id}");
                }
            } catch (\Exception $e) {
                $this->error("Error processing Activity ID {$activity->id}: " . $e->getMessage());
                Log::error("Notification failed for activity {$activity->id}: " . $e->getMessage());
            }
        });

        $this->info('Notification process completed!');
    }
}
