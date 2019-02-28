<?php declare(strict_types = 1);

namespace App\Console;

use App\Facades\ApiHelper;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Passport\Console\ClientCommand;
use function base_path;

/**
 * Class Kernel
 *
 * @package App\Console
 */
class Kernel extends \Illuminate\Foundation\Console\Kernel
{

    /** @var string[] */
    protected $commands = [
        ClientCommand::class
    ];


    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     */
    protected function schedule(Schedule $schedule): void
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
    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');

        include base_path('routes/console.php');
    }
}
