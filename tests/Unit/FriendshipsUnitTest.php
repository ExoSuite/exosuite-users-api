<?php

namespace Tests\Unit;

use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use Webpatser\Uuid\Uuid;

class FriendshipsUnitTest extends TestCase
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
    public function testSendFriendshipRequestWithWrongTarget()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_friendship_request', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testAcceptFriendshipRequestWithWrongRequestId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_accept_friendship_request', ['request' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testAcceptFriendshipRequestAsWrongTarget()
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('post_friendship_request', ['user' => $this->user1->id]));
        $response = $this->post(route('post_accept_friendship_request', ['request' => $post_resp->decodeResponseJson('request_id')]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "You're not allowed to answer this request"]);
    }

    public function testDeclineFriendshipRequestWithWrongRequestId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_decline_friendship_request', ['request' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeclineFriendshipRequestAsWrongTarget()
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('post_friendship_request', ['user' => $this->user1->id]));
        $response = $this->post(route('post_decline_friendship_request', ['request' => $post_resp->decodeResponseJson('request_id')]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "You're not allowed to answer this request"]);
    }

    public function testGetFriendshipsFromWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_friendships', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteFriendshipsWithWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_friendship', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteFalseFriendships()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_friendship', ['user' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson(['message' => "There is no such relation between you and this user."]);
    }
}
