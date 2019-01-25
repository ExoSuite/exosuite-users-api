<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

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
        $this->assertDatabaseMissing('users', ['email' => $this->faker->safeEmail]);
    }
}
