<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

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
     * @return string
     */
    protected function route($route, $parameters = [])
    {
        return route($route, $parameters, false);
    }
}
