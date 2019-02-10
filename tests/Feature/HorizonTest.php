<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class HorizonTest
 * @package Tests\Feature
 */
class HorizonTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testViewHorizonDashboard()
    {
        $response = $this->get($this->route('horizon.index'));
        $response->assertStatus(Response::HTTP_OK);
    }
}
