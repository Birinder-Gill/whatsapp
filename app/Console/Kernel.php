<?php

namespace App\Console;

use App\Console\Commands\ContactSaveFollowUp;
use App\Console\Commands\FollowUpConversations;
use App\Console\Commands\GainToTrain;
use App\Console\Commands\GetAllChats;
use App\Console\Commands\GetAllMessages;
use App\Console\Commands\LeadSystem;
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
        if (config('app.product') === "Tags") {
            $schedule->command(LeadSystem::class)->daily();
        }
        $schedule->command(GetAllChats::class, ["fromScheduler" => true])->dailyAt('23:05');
        $schedule->command(GetAllMessages::class, ["fromScheduler" => true])->dailyAt('23:00');
        $schedule->command(GainToTrain::class, ["fromScheduler" => true])->dailyAt('23:50');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
