<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\Friendship;
use App\Models\PendingRequest;
use App\Models\User;
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

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $user1;

    /**
     * A basic test example.
     *
     * @return void
     */
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

        Passport::actingAs($this->user);
        $response = $this->post(
            route(
                'post_accept_friendship_request',
                ['request' => $send_resp->decodeResponseJson('request_id')]
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

        Passport::actingAs($this->user);
        $response = $this->post(
            route(
                'post_decline_friendship_request',
                ['user' => $this->user->id, 'request' => $send_resp->decodeResponseJson('request_id')]
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
        $this->assertEquals(3, count($response->decodeResponseJson()));
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
        $this->assertEquals(3, count($response->decodeResponseJson()));
    }

    public function testDeleteFriendship(): void
    {
        Passport::actingAs($this->user1);
        $send_resp = $this->post(route('post_friendship_request', ['user' => $this->user->id]));

        Passport::actingAs($this->user);
        $accept_resp = $this->post(
            route(
                'post_accept_friendship_request',
                ['request' => $send_resp->decodeResponseJson('request_id')]
            )
        );
        $this->assertDatabaseHas('friendships', $accept_resp->decodeResponseJson());
        $response = $this->delete(route('delete_friendship', ['user' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('friendships', $accept_resp->decodeResponseJson());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
    }
}
