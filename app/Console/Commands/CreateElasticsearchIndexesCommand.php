<?php declare(strict_types = 1);

namespace App\Console\Commands;

use App\Services\ClassFinder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Throwable;

/**
 * Class CreateElasticsearchIndexesCommand
 *
 * @package App\Console\Commands
 */
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

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $indexes = ClassFinder::getIndexesClasses();

        foreach ($indexes as $index) {
            try {
                Artisan::call(
                    'elastic:create-index',
                    ['index-configurator' => $index]
                );
                $this->output->success(Artisan::output());
            } catch (Throwable $e) {
                $this->output->success("{$index} already created!");
            }
        }
    }
}
