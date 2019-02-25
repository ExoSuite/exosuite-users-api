<?php declare(strict_types = 1);

namespace App\Http\Middleware;

/**
 * Class CheckForMaintenanceMode
 *
 * @package App\Http\Middleware
 */
class CheckForMaintenanceMode extends \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode
{

    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [];
}
