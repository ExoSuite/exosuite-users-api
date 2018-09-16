<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $this->assertDatabaseMissing('users', [ 'email' => $this->faker->safeEmail ]);
    }
}
