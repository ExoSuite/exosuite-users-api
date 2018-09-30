<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Laravel\Passport\Client;

/**
 * Class UserTest
 * @package Tests\Feature
 */
class UserTest extends TestCase
{

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $userPassword = null;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        /* @var User $userData */
        $user = factory(User::class)->make();
        /* @var array $userData */
        $userData = $user->toArray();
        $userData['password'] = $user->password;
        $this->userPassword = $user->password;

        $this->user = User::create($userData);
    }

    /**
     *
     */
    public function testLoginMustReturnTokens()
    {
        $response = $this->json(
            Request::METHOD_POST,
            route('login'),
            [
                'email' => $this->user->email,
                'password' => $this->userPassword,
                'client_id' => 2,
                'client_secret' => Client::whereId(2)->first()->secret
            ]
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(
            [
                "token_type", "expires_in", "access_token", "refresh_token"
            ]
        );
    }

    public function testGetPersonalInfos()
    {
        Passport::actingAs(factory(User::class)->create());

        $response = $this->get(
            route('personal_user_infos')
        );
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testSearchUser()
    {
        Passport::actingAs($this->user);
        $route = route('user_search');
        $queries = http_build_query(['text' => $this->user->first_name]);

        $uri = "$route?$queries";

        $response = $this->json(
            Request::METHOD_GET,
            $uri
        );

        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure([
            [
                'id',
                'first_name',
                'last_name',
                'email',
                'nick_name'
            ]
        ]);
    }
}
