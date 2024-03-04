<?php

namespace App\Console;

use App\Console\Commands\ContactSaveFollowUp;
use App\Console\Commands\FollowUpConversations;
use App\Console\Commands\SecondFollowUp;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command(FollowUpConversations::class)->everyFiveMinutes();
        $schedule->command(SecondFollowUp::class)->daily();
        $schedule->command(ContactSaveFollowUp::class)->daily();
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
