<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Crons;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        try {
            return;
        }finally {
            $schedule->call(function () {
                Crons::everyMinute();
            })->everyMinute();
            $schedule->call(function () {
                Crons::everyFiveMinutes();
            })->everyFiveMinutes();
            $schedule->call(function () {
                Crons::hourly();
            })->hourly();
            $schedule->call(function () {
                Crons::daily();
            })->daily();
            $schedule->call(function () {
                Crons::monthly();
            })->monthly();
        }
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
