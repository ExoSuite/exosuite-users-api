<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * Class ScheduleDaemonCommand
 * @package App\Console\Commands
 */
class ScheduleDaemonCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'schedule:daemon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run the schedule daemon';

    /**
     * The interval (in seconds) the scheduler is run daemon mode.
     *
     * @var int
     */
    const SCHEDULE_INTERVAL = 30;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        while (true) {
            $start = time();
            $this->call('schedule:run');

            $sleepTime = max(0, self::SCHEDULE_INTERVAL - (time() - $start));
            if (0 == $sleepTime) {
                $this->error(sprintf(
                    'schedule:run did not finish in %d seconds. Some events might have been skipped.',
                    self::SCHEDULE_INTERVAL
                ));
            }
            sleep($sleepTime);
        }
    }
}
