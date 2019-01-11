<?php

namespace App\Console\Commands;

use App\Services\ClassFinder;
use Elasticsearch\Common\Exceptions\BadRequest400Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class CreateElasticsearchIndexesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'elastic:create-indexes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all ExoSuite ElasticSearchIndexes';

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
        $indexes = ClassFinder::getIndexesClasses();
        foreach ($indexes as $index) {
            try {
                Artisan::call(
                    'elastic:create-index',
                    ['index-configurator' => $index]
                );
                $this->output->success(Artisan::output());
            } catch (BadRequest400Exception $e) {
                $this->output->success("{$index} already created!");
            }
        }
    }
}
