<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class ProtectedRoutesTest
 * @package Tests\Unit
 */
class ProtectedRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAuthException()
    {
        $response = $this->json(Request::METHOD_GET, route('get_user'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
