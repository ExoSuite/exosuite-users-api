<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\RequestTypesEnum;
use App\Models\PendingRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class PendingRequestsTest
 *
 * @package Tests\Feature
 */
class PendingRequestsTest extends TestCase
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
     */
    public function testCreatePendinRequest(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            route(
                'post_pending_request',
                ['user' => $this->user1->id]
            ),
            ['type' => RequestTypesEnum::FRIENDSHIP_REQUEST]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new PendingRequest)->getFillable());
        $this->assertDatabaseHas('pending_requests', $response->decodeResponseJson());
    }

    public function testGetMyPendingRequests(): void
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        factory(PendingRequest::class)->create([
            'requester_id' => $this->user1->id,
            'type' => RequestTypesEnum::FRIENDSHIP_REQUEST,
            'target_id' => $this->user->id,
        ]);
        factory(PendingRequest::class)->create([
            'requester_id' => $user2->id,
            'type' => RequestTypesEnum::FRIENDSHIP_REQUEST,
            'target_id' => $this->user->id,
        ]);
        factory(PendingRequest::class)->create([
            'requester_id' => $user3->id,
            'type' => RequestTypesEnum::FRIENDSHIP_REQUEST,
            'target_id' => $this->user->id,
        ]);

        Passport::actingAs($this->user);
        $response = $this->get(route('get_my_pending_request'));
        $response->decodeResponseJson(Response::HTTP_OK);
        $this->assertEquals(3, count($response->decodeResponseJson()));
    }

    public function testDeletePendingRequest(): void
    {
        Passport::actingAs($this->user1);
        $post_resp = $this->post(
            route('post_pending_request', ['user' => $this->user->id]),
            ['type' => RequestTypesEnum::FRIENDSHIP_REQUEST]
        );
        Passport::actingAs($this->user);
        $response = $this->delete(
            route(
                'delete_pending_request',
                ['request' => $post_resp->decodeResponseJson('request_id')]
            )
        );
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('pending_requests', $post_resp->decodeResponseJson());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
    }
}
