<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserSearchTest
 *
 * @package Tests\Feature
 */
class UserSearchTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @var \App\Models\User */
    private $user;

    public function testSearchUser(): void
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
            $response->assertJsonStructure(['data' => []]);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->make();
        $this->user->nick_name = $this->faker->company;
        $user = User::create($this->user->toArray());
        $user->searchable();
    }
}
