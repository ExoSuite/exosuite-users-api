<?php

namespace Tests\Unit;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webpatser\Uuid\Uuid;

class FollowsUnitTest extends TestCase
{
    private $user;

    private $user1;

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
     */
    public function testFollowWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('follow', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testFollowAFollowedUser()
    {
        Passport::actingAs($this->user);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $this->user1->id]);
        $response = $this->post(route('follow', ['user' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson(['message' => "You're already following this user."]);
    }

    public function testUnfollowWithWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('unfollow', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUselessUnfollow()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('unfollow', ['user' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson(['message' => "You're not following this user."]);
    }

    public function testGetFollowersFromWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('followers', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testAmIFollowingAWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('amIFollowing', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
