<?php

namespace Tests\Unit;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProtectedRoutesTest extends TestCase
{
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
