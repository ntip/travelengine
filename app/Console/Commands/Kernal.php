<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Once a day is probably enough for rewards data, but
        // you can make this hourly if you want.
        $schedule->command('routes:sync-jobs')->dailyAt('02:00');

        // Or:
        // $schedule->command('routes:sync-jobs')->hourly();
    }

    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
