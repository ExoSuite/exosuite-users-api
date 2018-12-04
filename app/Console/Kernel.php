<?php

namespace App\Console;

use App\Facades\ApiHelper;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\App;
use Laravel\Passport\Console\ClientCommand;

/**
 * Class Kernel
 * @package App\Console
 */
class Kernel extends ConsoleKernel
{

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ClientCommand::class
    ];


    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        if (ApiHelper::isProduction() or ApiHelper::isStaging()) {
            $schedule->command('horizon:snapshot')->everyFiveMinutes();
        } else {
            $schedule->command('horizon:snapshot')->everyMinute();
        }

        $schedule->command('telescope:prune')->daily();
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        include base_path('routes/console.php');
    }
}
