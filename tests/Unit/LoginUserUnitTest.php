<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Class LoginUserUnitTest
 * @package Tests\Unit
 */
class LoginUserUnitTest extends TestCase
{
    use WithFaker;


    /**
     * @var array
     */
    protected $user;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        $this->setUpFaker();
        /** @var User $user */
        $user = factory(User::class)->make();
        $this->user = array_merge($user->toArray(), [ 'password' => $user[ 'password_confirmation' ] ]);
        $this->user = array_except($this->user, [ 'password_confirmation' ]);
        $this->user[ 'password' ] = Hash::make($this->user[ 'password' ]);
        User::create($this->user);
    }

    /**
     * @param $data
     * @param $status
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function request($data, $status)
    {
        $response = $this->json(Request::METHOD_POST, route('login'), $data);
        $response->assertStatus($status);
        return $response;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBadCredentialMustFail()
    {
        $this->request(
            [
                'email' => $this->faker->email,
                'password' => $this->faker->password
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    public function testBadPasswordMustFail()
    {
        $this->request(
            [
                'email' => $this->user[ 'email' ],
                'password' => $this->faker->password
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }
}
