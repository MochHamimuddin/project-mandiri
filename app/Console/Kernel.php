<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('check:deadline')
            ->dailyAt('19:21')
            ->timezone('Asia/Jakarta');
        $schedule->command('fatigue:check-deadlines')
            ->everyMinute()
            ->runInBackground() // Untuk efisiensi
            ->appendOutputTo(storage_path('logs/notifications.log'));

        $schedule->command('send:inspeksi-notifications')
            ->everyMinute()
            ->runInBackground();
        }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
