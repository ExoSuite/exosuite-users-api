<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Laravel\Passport\Passport;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\UserProfile;

class UserSearchTest extends TestCase
{
    use WithFaker;
    /**
     * @var User
     */
    private $user;

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


    public function testSearchUser()
    {
        Passport::actingAs($this->user);
        $userQueries = [
            'first_name',
            'last_name',
            'nick_name',
        ];
        $route = route('get_users');
        sleep(2); // wait for elastic search

        foreach ($userQueries as $param) {
            $queries = http_build_query(['text' => $this->user->{$param}]);

            $uri = "$route?$queries";

            $response = $this->get(
                $uri
            );

            $expectTo = array_diff((new User())->getFillable(), (new User())->getHidden());
            $expectTo['profile'] = (new UserProfile())->getFillable();

            $response->assertStatus(Response::HTTP_OK);
            $response->assertJsonStructure([$expectTo]);
        }
    }
}
