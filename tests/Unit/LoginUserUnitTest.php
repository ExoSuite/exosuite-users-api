<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class LoginUserUnitTest
 * @package Tests\Unit
 */
class LoginUserUnitTest extends TestCase
{
    use WithFaker;


    /**
     * @var User
     */
    protected $user;

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
     *
     */
    public function testBadPasswordMustFail()
    {
        $response = $this->request(
            [
                'email' => $this->user['email'],
                'password' => $this->faker->password
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJsonValidationErrors(['client_secret', 'client_id']);
    }

    /**
     *
     */
    public function testInvalidOAuthClient()
    {
        $response = $this->request(
            [
                'email' => $this->user->email,
                'password' => $this->user->getAuthPassword(),
                'client_id' => rand(0, 10),
                'client_secret' => str_random()
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->make();
        User::create($this->user->toArray());
    }
}
