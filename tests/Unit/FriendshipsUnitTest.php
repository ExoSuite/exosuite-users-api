<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class FriendshipsUnitTest
 *
 * @package Tests\Unit
 */
class FriendshipsUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $user1;

    /**
     * A basic test example.
     *
     * @return void
     * @throws \Exception
     */
    public function testSendFriendshipRequestWithWrongTarget(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_friendship_request', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testAcceptFriendshipRequestWithWrongRequestId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_accept_friendship_request', [
            'user' => $this->user,
            'request' => Uuid::generate()->string,
        ]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testAcceptFriendshipRequestAsWrongTarget(): void
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('post_friendship_request', ['user' => $this->user1->id]));
        $response = $this->post(
            route('post_accept_friendship_request', ['request' => $post_resp->decodeResponseJson('id')])
        );
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws \Exception
     */
    public function testDeclineFriendshipRequestWithWrongRequestId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_decline_friendship_request', ['request' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeclineFriendshipRequestAsWrongTarget(): void
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('post_friendship_request', ['user' => $this->user1->id]));
        $response = $this->post(
            route(
                'post_decline_friendship_request',
                ['request' => $post_resp->decodeResponseJson('id')]
            )
        );
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws \Exception
     */
    public function testGetFriendshipsFromWrongUser(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_friendships', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteFalseFriendships(): void
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_friendship', ['friendship' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetMyNonExistantFriendshipBecauseFriendshipRequestWasNotAccepted(): void
    {
        Passport::actingAs($this->user1);
        $this->post(route('post_friendship_request', ['user' => $this->user->id]));
        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_my_friendship_with', ['user' => $this->user1->id]));
        $this->assertEquals($response->decodeResponseJson('value'), false);
        $this->assertEquals($response->decodeResponseJson('friendship_entity'), null);
    }

    public function testGetMyNonExistantFriendshipBecauseNoFriendshipRequestWasSent(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_my_friendship_with', ['user' => $this->user1->id]));
        $this->assertEquals($response->decodeResponseJson('value'), true);
        $this->assertEquals($response->decodeResponseJson('friendship_entity'), null);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
    }
}
