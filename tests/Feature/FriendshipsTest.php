<?php

namespace Tests\Feature;

use App\Models\Friendship;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class FriendshipsTest extends TestCase
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
    public function testSendRequest()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('sendFriendshipRequest'), ['target_id' => $this->user1->id]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new PendingRequest())->getFillable());
        $this->assertDatabaseHas('pending_requests', $response->decodeResponseJson());
    }

    public function testAccept()
    {
        Passport::actingAs($this->user1);
        $send_resp = $this->post(route('sendFriendshipRequest'), ['target_id' => $this->user->id]);

        Passport::actingAs($this->user);
        $response = $this->post(route('acceptFriendship'), ['request_id' => $send_resp->decodeResponseJson('request_id')]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new Friendship())->getFillable());
        $this->assertDatabaseHas('friendships', $response->decodeResponseJson());
        $this->assertDatabaseMissing('pending_requests', $send_resp->decodeResponseJson());
    }

    public function testDecline()
    {
        Passport::actingAs($this->user1);
        $send_resp = $this->post(route('sendFriendshipRequest'), ['target_id' => $this->user->id]);

        Passport::actingAs($this->user);
        $response = $this->post(route('declineFriendship'), ['request_id' => $send_resp->decodeResponseJson('request_id')]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('pending_requests', $send_resp->decodeResponseJson());
    }

    public function testGetMyFriends()
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $this->user1->id]);
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $user2->id]);
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $user3->id]);

        Passport::actingAs($this->user);
        $response = $this->get(route('myFriendList'));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(3, count($response->decodeResponseJson()));
    }

    public function testGetSomeonesFriends()
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $this->user1->id]);
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $user2->id]);
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $user3->id]);

        Passport::actingAs($this->user1);
        $response = $this->get(route('friendList', ['target_id' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(3, count($response->decodeResponseJson()));
    }

    public function testDeleteFriendship()
    {

        Passport::actingAs($this->user1);
        $send_resp = $this->post(route('sendFriendshipRequest'), ['target_id' => $this->user->id]);

        Passport::actingAs($this->user);
        $accept_resp = $this->post(route('acceptFriendship'), ['request_id' => $send_resp->decodeResponseJson('request_id')]);
        $this->assertDatabaseHas('friendships', $accept_resp->decodeResponseJson());
        $response = $this->delete(route('deleteFriendship', ['target_id' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('friendships', $accept_resp->decodeResponseJson());
    }
}
