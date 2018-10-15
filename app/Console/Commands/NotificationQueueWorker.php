<?php

namespace App\Console\Commands;

use App\Enums\Queue;
use Illuminate\Console\Command;

class NotificationQueueWorker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run notification queue';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('queue:work', ['--queue' => Queue::NOTIFICATION]);
    }
}
