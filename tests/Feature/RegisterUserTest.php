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
        /* @var User $userData */
        $user = factory(User::class)->make();
        /* @var array $userData */
        $userData = $user->toArray();
        $userData['password'] = $user->password;
        $userData['password_confirmation'] = $user->password;

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
