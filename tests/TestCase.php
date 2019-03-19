<?php declare(strict_types = 1);

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

/**
 * Class TestCase
 *
 * @package Tests
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    /**
     * @param string $route
     * @param mixed[] $parameters
     * @param bool $absolute
     * @return string
     */
    protected function route(string $route, array $parameters = [], bool $absolute = false): string
    {
        return route($route, $parameters, $absolute);
    }
}
