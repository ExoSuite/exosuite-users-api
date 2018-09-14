<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class RegisterUserTest
 * @package Tests\Feature
 */
class RegisterUserTest extends TestCase
{

    protected function setUp()
    {
        parent::setUp();
        $this->artisan('passport:install');
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testRegisterUser()
    {
        $userData = factory(User::class)->make()->toArray();
        $userData[ 'password' ] = $userData[ 'password_confirmation' ];

        $response = $this->json(Request::METHOD_POST, 'register', $userData);

        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure([
            'token_type',
            'expires_in',
            'access_token',
            'refresh_token'
        ]);

        $userData = array_except($userData, [ 'password_confirmation', 'password' ]);
        $this->assertDatabaseHas('users', $userData);
    }
}
