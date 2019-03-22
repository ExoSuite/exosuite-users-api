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

    /**
     * @param array<mixed> $data
     * @param string $reference
     * @param string $foreign_key
     *
     * @return void
     */
    protected function assertForeignKeyInArray(array $data, string $reference, string $foreign_key): void
    {
        for ($i = 0; $i < count($data); $i++) {
            $this->assertForeignKeyIsExpectedID($data[$i][$foreign_key], $reference);
        }
    }

    protected function assertForeignKeyIsExpectedID(string $reference, string $foreign_key): void
    {
        $this->assertTrue($foreign_key === $reference);
    }
}
