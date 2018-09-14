<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class RegisterUserUnitTest
 * @package Tests\Unit
 */
class RegisterUserUnitTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    /**
     * @param $expected
     * @param array $data
     */
    private function request($expected, $data = [])
    {
        $response = $this->json(Request::METHOD_POST, 'register', $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonStructure(
            [
                'message', 'errors' => $expected
            ]
        );
    }

    /**
     * Assert if error will be sent
     *
     * @return void
     */
    public function testRegisterUserWithInvalidData()
    {
        $this->request([ 'first_name', 'last_name', 'password', 'email' ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLoopWithInvalidData()
    {
        /* @var array $userData */
        $userData = factory(User::class)->make()->toArray();
        $userData[ 'password' ] = $userData[ 'password_confirmation' ];
        $userData = array_except($userData, [ 'password_confirmation' ]);

        $data = array_keys($userData);
        foreach ( $userData as $key => $value ) {
            $this->request($data, $data);
            $data = array_diff($data, [ $key ]);
        }

        $data = [ 'password_confirmation' => $userData[ 'password' ] ];
        $this->request([ 'password' ], $data);

        $userData[ 'password_confirmation' ] = str_random();
        $this->request([ 'password' ], $userData);
    }

    public function testRegisterUserWithoutPassport()
    {
        $userData = factory(User::class)->make()->toArray();
        $userData[ 'password' ] = $userData[ 'password_confirmation' ];

        $response = $this->json(Request::METHOD_POST, 'register', $userData);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
