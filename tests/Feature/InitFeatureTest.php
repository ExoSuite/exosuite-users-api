<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class InitFeatureTest
 * @package Tests\Feature
 */
class InitFeatureTest extends TestCase
{
    use WithFaker;

    public function testDatabaseConnection()
    {
        $this->artisan('passport:install');
        $this->assertDatabaseMissing('users', [ 'email' => $this->faker->safeEmail ]);
    }
}
