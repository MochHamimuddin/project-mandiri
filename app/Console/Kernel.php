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
        \App\Console\Commands\SendFireNotifications::class,
        \App\Console\Commands\SendKesehatanNotifications::class,
        \App\Console\Commands\SendLingkunganHidupNotifications::class,
        \App\Console\Commands\SendDevelopmentManpowerNotifications::class
        // Add other commands here if needed
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {

        // ================= Schedule untuk fatigue activities ======================
        $schedule->command('check:deadline fatigue')
            ->dailyAt('15:05')
            ->timezone('Asia/Jakarta');

        $schedule->command('notifications:fatigue')
        ->everyMinute() // Run at 12:00 WIB daily
        ->timezone('Asia/Jakarta');

        $schedule->command('send:inspeksi-notifications')
            ->everyMinute()
            ->runInBackground();


        // ================= Schedule untuk Fire Preventive ======================
        $schedule->command('check:deadline fire')
            ->monthlyOn(16, '19:00')
            ->timezone('Asia/Jakarta');

        $schedule->command('notifications:fire')
        //  ->everyMinute()
        ->monthlyOn(17, '04:00')
        ->timezone('Asia/Jakarta');

        // $schedule->command('send:fire-preventive-notifications')
        //     ->everyMinute()
        //     ->runInBackground();


                // ================= Schedule untuk Program Lingkungan Hidup ======================
            $schedule->command('notifications:lingkungan')
            //->everyMinute() // Run at 12:00 WIB daily
            ->weeklyOn(4, '04:00')
            ->timezone('Asia/Jakarta');


                                // ================= Schedule untuk Program Kerja Kesehatan ======================
                    
                            $schedule->command('notifications:kesehatan')
                            //->everyMinute() // Run at 12:00 WIB daily
                            ->yearlyOn(7, 17, '04:00')
                            ->timezone('Asia/Jakarta');

                                                            // ================= Schedule untuk Development Power ======================
                                                            // $schedule->command('check:deadline kesehatan')
                                                            // ->weeklyOn(6, '8:00')
                                                            // ->timezone('Asia/Jakarta');
                                                
                                                        $schedule->command('notifications:development')
                                                        //->everyMinute() // Run at 12:00 WIB daily
                                                        ->monthlyOn(17, '04:00')
                                                        ->timezone('Asia/Jakarta');
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
