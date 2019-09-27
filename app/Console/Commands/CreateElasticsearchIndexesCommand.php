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

    private const PER_PAGE = 50;

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
            $completeModel = "$modelNamespace$model";
            /** @var \Illuminate\Database\Eloquent\Model|\ScoutElastic\Searchable $instance */
            $instance = new $completeModel;

            try {
                Artisan::call(
                    'elastic:update-mapping',
                    ['model' => "{$modelNamespace}{$model}"]
                );
                $this->info("The {$model} mapping was updated");
            } catch (Throwable $e) {
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

            /** @var \Illuminate\Pagination\LengthAwarePaginator $models */
            $models = $instance->all()->paginate(self::PER_PAGE);
            $total = $models->total();
            $currentPage = $models->currentPage();
            $lastPage = $models->lastPage();

            while ($currentPage <= $lastPage) {
                $this->info("{$completeModel}: Processing page {$currentPage} of {$lastPage}");
                $models->searchable();
                $models = $instance->all()->paginate(self::PER_PAGE, $total, $currentPage);
                $currentPage++;
            }
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
