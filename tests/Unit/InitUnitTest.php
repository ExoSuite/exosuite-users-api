<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * Class InitUnitTest
 * @package Tests\Unit
 */
class InitUnitTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDatabaseConnection()
    {
        $this->assertDatabaseMissing('users', ['email' => $this->faker->safeEmail]);
    }
}
