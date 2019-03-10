<?php declare(strict_types = 1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use function sleep;
use function sprintf;
use function time;

/**
 * Class ScheduleDaemonCommand
 *
 * @package App\Console\Commands
 */
class ScheduleDaemonCommand extends Command
{

    /**
     * The interval (in seconds) the scheduler is run daemon mode.
     */
    private const SCHEDULE_INTERVAL = 30;

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
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        while (true) {
            $start = time();
            $this->call('schedule:run');

            $sleepTime = max(0, self::SCHEDULE_INTERVAL - (time() - $start));

            if ($sleepTime === 0) {
                $this->error(sprintf(
                    'schedule:run did not finish in %d seconds. Some events might have been skipped.',
                    self::SCHEDULE_INTERVAL
                ));
            }

            sleep($sleepTime);
        }
    }
}
