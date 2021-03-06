<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\Friendship;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class FriendshipsTest
 *
 * @package Tests\Feature
 */
class FriendshipsTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $user1;

    public function testSendRequest(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_friendship_request', ['user' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new PendingRequest)->getFillable());
        $this->assertDatabaseHas('pending_requests', $response->decodeResponseJson());
    }

    public function testAccept(): void
    {
        Passport::actingAs($this->user1);
        $send_resp = $this->post(route('post_friendship_request', ['user' => $this->user->id]));
        $send_resp->assertStatus(Response::HTTP_CREATED);

        Passport::actingAs($this->user);
        $response = $this->post(
            route(
                'post_accept_friendship_request',
                ['request' => $send_resp->decodeResponseJson('id')]
            )
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new Friendship)->getFillable());
        $this->assertDatabaseHas('friendships', $response->decodeResponseJson());
        $this->assertDatabaseMissing('pending_requests', $send_resp->decodeResponseJson());
    }

    public function testDecline(): void
    {
        Passport::actingAs($this->user1);
        $send_resp = $this->post(route('post_friendship_request', ['user' => $this->user->id]));
        $send_resp->assertStatus(Response::HTTP_CREATED);

        Passport::actingAs($this->user);
        $response = $this->post(
            route(
                'post_decline_friendship_request',
                ['user' => $this->user->id, 'request' => $send_resp->decodeResponseJson('id')]
            )
        );
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('pending_requests', $send_resp->decodeResponseJson());
    }

    public function testGetMyFriends(): void
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $this->user1->id]);
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $user2->id]);
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $user3->id]);

        Passport::actingAs($this->user);
        $response = $this->get(route('get_my_friendships'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            "user_id" => $this->user->id,
            "friend_id" => $this->user1->id,
            "first_name" => $this->user1->first_name,
            "last_name" => $this->user1->last_name,
        ]);
    }

    public function testGetSomeonesFriends(): void
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $this->user1->id]);
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $user2->id]);
        factory(Friendship::class)->create(['user_id' => $this->user->id, 'friend_id' => $user3->id]);

        Passport::actingAs($this->user1);
        $response = $this->get(route('get_friendships', ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            "user_id" => $this->user->id,
            "friend_id" => $this->user1->id,
            "first_name" => $this->user1->first_name,
            "last_name" => $this->user1->last_name,
        ]);
    }

    public function testDeleteFriendship(): void
    {
        Passport::actingAs($this->user1);
        $send_resp = $this->post(route('post_friendship_request', ['user' => $this->user->id]));
        Passport::actingAs($this->user);
        $accept_resp = $this->post(
            route(
                'post_accept_friendship_request',
                ['request' => $send_resp->decodeResponseJson('id')]
            )
        );
        $this->assertDatabaseHas('friendships', $accept_resp->decodeResponseJson());
        $response = $this->delete(route('delete_friendship', ['friendship' => $accept_resp->decodeResponseJson('id')]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('friendships', $accept_resp->decodeResponseJson());
    }

    public function testGetMyFriendship(): void
    {
        Passport::actingAs($this->user1);
        $send_resp = $this->post(route('post_friendship_request', ['user' => $this->user->id]));
        Passport::actingAs($this->user);
        $friendship = $this->post(
            route(
                'post_accept_friendship_request',
                ['request' => $send_resp->decodeResponseJson('id')]
            )
        );
        Passport::actingAs($this->user1);
        $response = $this->get($this->route('get_my_friendship_with', ['user' => $this->user->id]));
        $this->assertEquals($response->decodeResponseJson('value'), false);
        $this->assertEquals($response->decodeResponseJson('friendship_entity'), $friendship->decodeResponseJson());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
    }
}
