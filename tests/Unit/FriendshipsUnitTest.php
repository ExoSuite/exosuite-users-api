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
        $response = $this->post(route('sendFriendshipRequest'), ['target_id' => Uuid::generate()->string]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testAcceptFriendshipRequestWithWrongRequestId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('acceptFriendship'), ['request_id' => Uuid::generate()->string]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testAcceptFriendshipRequestAsWrongTarget()
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('sendFriendshipRequest'), ['target_id' => $this->user1->id]);
        $response = $this->post(route('acceptFriendship'), ['request_id' => $post_resp->decodeResponseJson('request_id')]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "You're not allowed to answer this request"]);
    }

    public function testDeclineFriendshipRequestWithWrongRequestId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('declineFriendship'), ['request_id' => Uuid::generate()->string]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeclineFriendshipRequestAsWrongTarget()
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('sendFriendshipRequest'), ['target_id' => $this->user1->id]);
        $response = $this->post(route('declineFriendship'), ['request_id' => $post_resp->decodeResponseJson('request_id')]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "You're not allowed to answer this request"]);
    }

    public function testGetFriendshipsFromWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('friendList', ['target_id' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteFriendshipsWithWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('deleteFriendship', ['target_id' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteFalseFriendships()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('deleteFriendship', ['target_id' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson(['message' => "There is no such relation between you and this user."]);
    }
}
