<?php declare(strict_types = 1);

namespace App\Console\Commands;

use App\Services\ClassFinder;
use Carbon\Carbon;
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
            /** @var string $index */
            $index = strrchr($index, "\\");
            $indexName = substr($index, 1);
            list($model) = explode(self::INDEX_PREFIX, $indexName);

            try {
                Artisan::call(
                    'elastic:update-mapping',
                    ['model' => "{$modelNamespace}{$model}"]
                );
                $this->info("The {$model} mapping was updated");
            } catch (Throwable $e) {
                $completeModel = "$modelNamespace$model";
                $instance = new $completeModel;
                $indexConfigurator = $instance->getIndexConfigurator();
                    Artisan::call(
                        'elastic:migrate',
                        [
                            'model' => "{$modelNamespace}{$model}",
                            "target-index" => $indexConfigurator->getName() . "_" . Carbon::now()->timestamp,
                        ]
                    );
                $this->info(Artisan::output());
            }

            Artisan::call(
                "scout:import",
                ['model' => "{$modelNamespace}{$model}"]
            );
            $this->info(Artisan::output());
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
