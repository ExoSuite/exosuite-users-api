<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Pagination\Paginator;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserSearchTest
 * @package Tests\Feature
 */
class UserSearchTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * @var User
     */
    private $user;

    /**
     *
     */
    public function testSearchUser()
    {
        Passport::actingAs($this->user);
        $userQueries = [
            'first_name',
            'last_name',
            'nick_name',
        ];
        $route = route('get_users');

        foreach ($userQueries as $param) {
            $queries = http_build_query(['text' => $this->user->{$param}]);

            $uri = "$route?$queries";

            $response = $this->get(
                $uri
            );


            $response->assertStatus(Response::HTTP_OK);
            $response->assertJsonStructure(["data" => []]);
        }
    }

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->make();
        $this->user->nick_name = $this->faker->company;
        $user = User::create($this->user->toArray());
        $user->searchable();
    }
}
