<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\Http;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\SendFatigueNotifications::class,
        // Add other commands here if needed
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('check:deadline')
            ->dailyAt('15:05')
            ->timezone('Asia/Jakarta');

        $schedule->command('notifications:fatigue')
        ->everyMinute() // Run at 12:00 WIB daily
        ->timezone('Asia/Jakarta');

        $schedule->command('send:inspeksi-notifications')
            ->everyMinute()
            ->runInBackground();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
