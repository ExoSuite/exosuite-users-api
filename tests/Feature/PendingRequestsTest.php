<?php

namespace Tests\Feature;

use App\Enums\RequestTypesEnum;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class PendingRequestsTest
 * @package Tests\Feature
 */
class PendingRequestsTest extends TestCase
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
     *
     */
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
        $response = $this->post(route('post_pending_request', ['user' => $this->user1->id]), ['type' => RequestTypesEnum::FRIENDSHIP_REQUEST]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new PendingRequest())->getFillable());
        $this->assertDatabaseHas('pending_requests', $response->decodeResponseJson());
    }

    /**
     *
     */
    public function testGetMyPendingRequests()
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        factory(PendingRequest::class)->create(['requester_id' => $this->user1->id, 'type' => RequestTypesEnum::FRIENDSHIP_REQUEST, 'target_id' => $this->user->id]);
        factory(PendingRequest::class)->create(['requester_id' => $user2->id, 'type' => RequestTypesEnum::FRIENDSHIP_REQUEST, 'target_id' => $this->user->id]);
        factory(PendingRequest::class)->create(['requester_id' => $user3->id, 'type' => RequestTypesEnum::FRIENDSHIP_REQUEST, 'target_id' => $this->user->id]);

        Passport::actingAs($this->user);
        $response = $this->get(route('get_my_pending_request'));
        $response->decodeResponseJson(Response::HTTP_OK);
        $this->assertEquals(3, count($response->decodeResponseJson()));
    }

    /**
     *
     */
    public function testDeletePendingRequest()
    {
        Passport::actingAs($this->user1);
        $post_resp = $this->post(route('post_pending_request', ['user' => $this->user->id]), ['type' => RequestTypesEnum::FRIENDSHIP_REQUEST]);
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_pending_request', ['request' => $post_resp->decodeResponseJson('request_id')]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('pending_requests', $post_resp->decodeResponseJson());
    }
}
