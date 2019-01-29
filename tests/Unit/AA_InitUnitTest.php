<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

// this class will be called first

/**
 * Class AA_InitUnitTest
 * @package Tests\Unit
 */
class AA_InitUnitTest extends TestCase
{
    use WithFaker;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testDatabaseConnection()
    {
        $this->InitTests();
        $this->assertDatabaseMissing('users', ['email' => $this->faker->safeEmail]);
    }
}
