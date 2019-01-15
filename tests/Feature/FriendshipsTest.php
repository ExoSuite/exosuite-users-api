<?php

namespace Tests\Feature;

use App\Constants\RequestTypes;
use App\Models\Friendship;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
        $response = $this->post($this->route('sendFriendshipRequest'), ['target_id' => $this->user1->id]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new PendingRequest())->getFillable());
    }

    public function testAccept()
    {
        Passport::actingAs($this->user);
        $request = factory(PendingRequest::class)->create();
        $request['requester_id'] = $this->user1->id;
        $request['type'] = RequestTypes::FRIENDSHIP_REQUEST;
        $request['target_id'] = $this->user->id;
        $response = $this->post($this->route('acceptFriendship'), ['request_id' => $request->id]);
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new Friendship())->getFillable());
    }

    public function testDecline()
    {
        Passport::actingAs($this->user);
        $request = factory(PendingRequest::class)->create();
        $request['requester_id'] = $this->user1->id;
        $request['type'] = RequestTypes::FRIENDSHIP_REQUEST;
        $request['target_id'] = $this->user->id;
        $response = $this->post($this->route('declineFriendship'), ['request_id' => $request->id]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);

    }

    public function testGetMyFriends()
    {
        Passport::actingAs($this->user);
        $response = $this->get($this->route('myFriendList'));
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testGetSomeonesFriends()
    {
        Passport::actingAs($this->user);
        $response = $this->get($this->route('friendList', ['target_id' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_OK);
    }
}
