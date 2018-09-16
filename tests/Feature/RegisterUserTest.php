<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class RegisterUserTest
 * @package Tests\Feature
 */
class RegisterUserTest extends TestCase
{
    /**
     * Register an user
     *
     * @return void
     */
    public function testRegisterUser()
    {
        $userData = factory(User::class)->make()->toArray();
        $userData[ 'password' ] = $userData[ 'password_confirmation' ];

        $response = $this->json(Request::METHOD_POST, route('register'), $userData);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(
            [
                'token_type', 'expires_in', 'access_token', 'refresh_token'
            ]
        );

        $userData = array_except($userData, [ 'password_confirmation', 'password' ]);
        $this->assertDatabaseHas('users', $userData);
    }
}
