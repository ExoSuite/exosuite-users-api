<?php declare(strict_types = 1);

namespace App\Models\Indexes;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

/**
 * Class UserIndexConfigurator
 *
 * @package App\Models\Indexes
 */
class UserIndexConfigurator extends IndexConfigurator
{

    use Migratable;

    /** @var array */
    protected $settings = [
    ];
}
