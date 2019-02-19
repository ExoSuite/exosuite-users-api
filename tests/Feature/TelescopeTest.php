<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class TelescopeTest
 * @package Tests\Feature
 */
class TelescopeTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testViewTelescopeDashboard()
    {
        $response = $this->get($this->route('telescope'));
        $response->assertStatus(Response::HTTP_OK);
    }
}
