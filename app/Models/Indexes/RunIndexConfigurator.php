<?php declare(strict_types = 1);

namespace App\Models\Indexes;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

/**
 * Class UserIndexConfigurator
 *
 * @package App\Models\Indexes
 */
class RunIndexConfigurator extends IndexConfigurator
{

    use Migratable;

    /** @var string[] */
    protected $settings = [];
}
