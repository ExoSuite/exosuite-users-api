<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class FollowsUnitTest
 *
 * @package Tests\Unit
 */
class FollowsUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /**
     * A basic test example.
     *
     * @return void
     * @throws \Exception
     */
    public function testFollowWrongUser(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_follow', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUselessUnfollow(): void
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_follow', ['follow' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testGetFollowersFromWrongUser(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_followers', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testAmIFollowingAWrongUser(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_am_i_following', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
