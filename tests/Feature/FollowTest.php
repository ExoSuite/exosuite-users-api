<?php

namespace Tests\Feature;

use App\Models\Follow;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class FollowTest extends TestCase
{
    private $user;

    private $user1;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFollowSomeone()
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route("follow"), ["followed_id" => $this->user1->id]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Follow())->getFillable());
    }

    public function testUnfollow()
    {
        Passport::actingAs($this->user);
        $this->post($this->route("follow"), ["followed_id" => $this->user1->id]);
        $response = $this->delete($this->route('unfollow'), ["followed_id" => $this->user1->id]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testGetFollowers()
    {
        Passport::actingAs($this->user);
        $follow = factory(Follow::class)->create();
        $follow['user_id'] = $this->user1->id;
        $follow['followed_id'] = $this->user->id;
        $response = $this->get($this->route("followers"), ["followed_id" => $this->user->id]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testAmIFollowing()
    {
        Passport::actingAs($this->user);
        $follow = factory(Follow::class)->create();
        $follow['user_id'] = $this->user1->id;
        $follow['followed_id'] = $this->user->id;
        $response = $this->get($this->route("amIFollowing"), ["followed_id" => $this->user->id]);
        $response->assertStatus(Response::HTTP_OK);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
    }
}
