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
        // Fetch weather data every hour
        $schedule->command('weather:fetch')
            ->hourly()
            ->runInBackground()
            ->withoutOverlapping();

        if ((bool) config('demo_activity.enabled')) {
            $schedule->command('demo:simulate-activity --maintain-future')
                ->hourly()
                ->withoutOverlapping();

            $schedule->command('demo:simulate-activity --add-today-users')
                ->dailyAt('00:10')
                ->withoutOverlapping();
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
