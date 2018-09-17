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
        $response = $this->json(Request::METHOD_GET, route('personal_user_infos'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
