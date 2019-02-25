<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\RequestTypesEnum;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class PendingRequestsUnitTest
 * @package Tests\Unit
 */
class PendingRequestsUnitTest extends TestCase
{
    use RefreshDatabase;

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
     */
    public function testCreationWithBadType(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_pending_request', [
            'user' => $this->user1->id
        ]), [
            'type' => 'wrong_type'
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors(['type']);
    }

    /**
     * @throws \Exception
     */
    public function testCreationWithWrongUserId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_pending_request', [
            'user' => Uuid::generate()->string
        ]), [
            'type' => RequestTypesEnum::FRIENDSHIP_REQUEST
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testDeleteWithBadRequestId(): void
    {
        Passport::actingAs($this->user1);
        $this->post(route('post_pending_request', [
            'user' => $this->user->id
        ]), [
            'type' => RequestTypesEnum::FRIENDSHIP_REQUEST
        ]);
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_pending_request', ['request' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteAsWrongTarget(): void
    {
        Passport::actingAs($this->user);
        $post_response = $this->post(route('post_pending_request', [
            'user' => $this->user1->id
        ]), [
            'type' => RequestTypesEnum::FRIENDSHIP_REQUEST
        ]);
        $response = $this->delete(route('delete_pending_request', [
            'request' => $post_response->decodeResponseJson('request_id')
        ]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
    }
}
