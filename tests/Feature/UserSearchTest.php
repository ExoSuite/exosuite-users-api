<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserSearchTest extends TestCase
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


    public function testSearchUser()
    {
        Passport::actingAs($this->user);
        $userQueries =  [
            'first_name',
            'last_name',
            'nick_name',
        ];
        $route = route('get_users');

        foreach ($userQueries as $param) {
            $queries = http_build_query(['text' => $this->user->{$param}]);

            $uri = "$route?$queries";

            $response = $this->json(
                Request::METHOD_GET,
                $uri
            );

            dd($response->decodeResponseJson());

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
}
