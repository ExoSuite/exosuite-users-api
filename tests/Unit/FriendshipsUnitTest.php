<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class FriendshipsUnitTest
 * @package Tests\Unit
 */
class FriendshipsUnitTest extends TestCase
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
     * A basic test example.
     *
     * @return void
     * @throws \Exception
     */
    public function testSendFriendshipRequestWithWrongTarget()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_friendship_request', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testAcceptFriendshipRequestWithWrongRequestId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_accept_friendship_request', [
            'user' => $this->user,
            'request' => Uuid::generate()->string
        ]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testAcceptFriendshipRequestAsWrongTarget()
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('post_friendship_request', ['user' => $this->user1->id]));
        $response = $this->post(route('post_accept_friendship_request', ['request' => $post_resp->decodeResponseJson('request_id')]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "You're not allowed to answer this request"]);
    }

    /**
     * @throws \Exception
     */
    public function testDeclineFriendshipRequestWithWrongRequestId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_decline_friendship_request', ['request' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testDeclineFriendshipRequestAsWrongTarget()
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('post_friendship_request', ['user' => $this->user1->id]));
        $response = $this->post(route('post_decline_friendship_request', ['request' => $post_resp->decodeResponseJson('request_id')]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "You're not allowed to answer this request"]);
    }

    /**
     * @throws \Exception
     */
    public function testGetFriendshipsFromWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_friendships', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testDeleteFriendshipsWithWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_friendship', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testDeleteFalseFriendships()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_friendship', ['user' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
        $response->assertJson(['message' => "There is no such relation between you and this user."]);
    }

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
    }
}
