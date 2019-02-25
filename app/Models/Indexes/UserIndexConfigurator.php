<?php declare(strict_types = 1);

namespace App\Models\Indexes;

use App\Models\Indexes\IndexConfigurator;
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
