<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\PendingCommand;

/**
 * Class TestCase
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param $route
     * @param array $parameters
     * @param bool $absolute
     * @return string
     */
    protected function route($route, $parameters = [], $absolute = false)
    {
        return route($route, $parameters, $absolute);
    }
}
