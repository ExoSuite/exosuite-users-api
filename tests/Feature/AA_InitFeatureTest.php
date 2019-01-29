<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;


// this class will be called first


/**
 * Class AA_InitFeatureTest
 * @package Tests\Feature
 */
class AA_InitFeatureTest extends TestCase
{
    use WithFaker;

    /**
     *
     */
    public function testDatabaseConnection()
    {
        $this->InitTests();
        $this->artisan('passport:install');
        $this->assertEmpty(User::all());
    }
}
