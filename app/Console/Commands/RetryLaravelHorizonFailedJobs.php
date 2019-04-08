<?php declare(strict_types = 1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Laravel\Horizon\Jobs\RetryFailedJob;

class RetryLaravelHorizonFailedJobs extends Command
{

    private const TABLE = "failed_jobs";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'horizon:failed-jobs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retry Horizon failed jobs';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $failedJobs = DB::table(self::TABLE)->get();

        foreach ($failedJobs as $failedJob) {
            dispatch(new RetryFailedJob($failedJob->id));
            $this->info("Retry job id: $failedJob->id...");
            DB::table(self::TABLE)->delete(['id' => $failedJob->id]);
            $this->info("Job id: $failedJob->id deleted!");
        }

        $self = self::class;
        $this->info("Finished running $self");
    }
}
