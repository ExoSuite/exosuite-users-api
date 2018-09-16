<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Response;

/**
 * Class LoginUserTest
 * @package Tests\Feature
 */
class LoginUserTest extends TestCase
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
        $this->user[ 'base_password' ] = $this->user[ 'password' ];
        $this->user[ 'password' ] = Hash::make($this->user[ 'password' ]);

        User::create($this->user);
    }

    /**
     *
     */
    public function testLoginMustReturnTokens()
    {
        $response = $this->json(Request::METHOD_POST, route('login'), [
            'email' => $this->user[ 'email' ],
            'password' => $this->user[ 'base_password' ]
        ]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            "token_type", "expires_in", "access_token", "refresh_token"
        ]);
    }
}
