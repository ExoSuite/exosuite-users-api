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

    private const MODEL_NAMESPACE = "App\\Models\\";

    private const INDEX_PREFIX = "IndexConfigurator";

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
        $modelNamespace = self::MODEL_NAMESPACE;

        foreach ($indexes as $index) {
            $this->createIndex($index);
            $indexName = substr(strrchr($index, "\\"), 1);
            list($model) = explode(self::INDEX_PREFIX, $indexName);

            Artisan::call(
                'elastic:update-mapping',
                ['model' => "{$modelNamespace}{$model}"]
            );
            $this->info("The {$model} mapping was updated");
        }
    }

    private function createIndex(string $index): void
    {
        try {
            Artisan::call(
                'elastic:create-index',
                ['index-configurator' => $index]
            );
            $this->info(Artisan::output());
        } catch (Throwable $e) {
            $this->info("{$index} already created!");
        }
    }
}
