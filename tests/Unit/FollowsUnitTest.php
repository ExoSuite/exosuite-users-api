<?php

namespace Tests\Unit;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class FollowsUnitTest
 * @package Tests\Unit
 */
class FollowsUnitTest extends TestCase
{
    /**
     * @var
     */
    private $user;

    /**
     * @var
     */
    private $user1;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
    }

    /**
     * A basic test example.
     *
     * @return void
     * @throws \Exception
     */
    public function testFollowWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_follow', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testFollowAFollowedUser()
    {
        Passport::actingAs($this->user);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $this->user1->id]);
        $response = $this->post(route('post_follow', ['user' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson(['message' => "You're already following this user."]);
    }

    /**
     * @throws \Exception
     */
    public function testUnfollowWithWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_follow', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testUselessUnfollow()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_follow', ['user' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson(['message' => "You're not following this user."]);
    }

    /**
     * @throws \Exception
     */
    public function testGetFollowersFromWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_followers', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testAmIFollowingAWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_am_i_following', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
