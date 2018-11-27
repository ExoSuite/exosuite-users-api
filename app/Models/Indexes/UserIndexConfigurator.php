<?php

namespace App\Models\Indexes;

use ScoutElastic\IndexConfigurator;
use ScoutElastic\Migratable;

class UserIndexConfigurator extends IndexConfigurator
{
    use Migratable;

    protected $name = 'user_index';

    /**
     * @var array
     */
    protected $settings = [
        //
    ];
}
