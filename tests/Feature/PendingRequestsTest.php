<?php

namespace Tests\Feature;

use App\Enums\RequestTypesEnum;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use App\Models\PendingRequest;

class PendingRequestsTest extends TestCase
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
    public function testCreatePendinRequest()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('createPending', ['user' => $this->user1->id]), ['type' => RequestTypesEnum::FRIENDSHIP_REQUEST]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new PendingRequest())->getFillable());
        $this->assertDatabaseHas('pending_requests', $response->decodeResponseJson());
    }

    public function testGetMyPendingRequests()
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        factory(PendingRequest::class)->create(['requester_id' => $this->user1->id, 'type' => RequestTypesEnum::FRIENDSHIP_REQUEST, 'target_id' => $this->user->id]);
        factory(PendingRequest::class)->create(['requester_id' => $user2->id, 'type' => RequestTypesEnum::FRIENDSHIP_REQUEST, 'target_id' => $this->user->id]);
        factory(PendingRequest::class)->create(['requester_id' => $user3->id, 'type' => RequestTypesEnum::FRIENDSHIP_REQUEST, 'target_id' => $this->user->id]);

        Passport::actingAs($this->user);
        $response = $this->get(route('getMyPendings'));
        $response->decodeResponseJson(Response::HTTP_OK);
        $this->assertEquals(3, count($response->decodeResponseJson()));
    }

    public function testDeletePendingRequest()
    {
        Passport::actingAs($this->user1);
        $post_resp = $this->post(route('createPending', ['user' => $this->user->id]), ['type' => RequestTypesEnum::FRIENDSHIP_REQUEST]);
        Passport::actingAs($this->user);
        $response = $this->delete(route('deletePending', ['request' => $post_resp->decodeResponseJson('request_id')]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('pending_requests', $post_resp->decodeResponseJson());
    }
}
