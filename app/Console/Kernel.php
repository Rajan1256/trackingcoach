<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('checker:timezones')->daily()->at('06:00');
        $schedule->command('reports:weekly')->saturdays()->at('12:00');
        $schedule->command('reports:weekly')->mondays()->at('12:00');
        $schedule->command('reports:monthly')->monthlyOn(1, '13:00');
        $schedule->command('reminders:reviews')->weekdays()->between('09:00', '16:30')->everyThirtyMinutes();
        $schedule->command('invites:send')->everyMinute();
        $schedule->command('invites:reminders')->everyMinute();
        $schedule->command('customer:quantity')->daily()->at('12:00');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
