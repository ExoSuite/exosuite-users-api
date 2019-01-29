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
     *
     */
    public function testRegisterUserWithReturnedUser()
    {
        $this->testRegisterUser(true);
    }

    /**
     * Register an user
     *
     * @param bool $with_user
     * @param bool $with_nick_name
     * @return void
     */
    public function testRegisterUser(bool $with_user = false, bool $with_nick_name = false)
    {
        /* @var User $user */
        $user = factory(User::class)->make();
        /* @var array $userData */
        $userData = $user->toArray();
        $userData['password'] = $user->password;
        $userData['password_confirmation'] = $user->password;

        if ($with_user) {
            $userData['with_user'] = true;
        }

        if ($with_nick_name) {
            $userData['nick_name'] = str_random();
        }

        $response = $this->json(Request::METHOD_POST, route('register'), $userData);
        $response->assertStatus(Response::HTTP_CREATED);
        $userData = array_except($userData, ['password_confirmation', 'password', 'with_user']);


        if ($with_user) {
            $structure = [
                'email', 'id', 'first_name', 'created_at', 'updated_at', 'last_name'
            ];

            if ($with_nick_name) {
                array_push($structure, 'nick_name');
            }

            $response->assertJsonStructure($structure);
        }

        $this->assertDatabaseHas('users', $userData);
    }

    /**
     *
     */
    public function testRegisterUserWithNickNameWithReturnedUser()
    {
        $this->testRegisterUser(true, true);
    }
}
