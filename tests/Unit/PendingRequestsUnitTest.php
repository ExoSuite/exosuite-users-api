<?php

namespace Tests\Unit;

use App\Enums\RequestTypesEnum;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use App\Models\User;
use Webpatser\Uuid\Uuid;

class PendingRequestsUnitTest extends TestCase
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
    public function testCreationWithBadType()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('createPending', ['user' => $this->user1->id]), ['type' => 'wrong_type']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreationWithWrongUserId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('createPending', ['user' => Uuid::generate()->string]), ['type' => RequestTypesEnum::FRIENDSHIP_REQUEST]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteWithBadRequestId()
    {
        Passport::actingAs($this->user1);
        $post_response = $this->post(route('createPending', ['user' => $this->user->id]), ['type' => RequestTypesEnum::FRIENDSHIP_REQUEST]);
        Passport::actingAs($this->user);
        $response = $this->delete(route('deletePending', ['request' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteAsWrongTarget()
    {
        Passport::actingAs($this->user);
        $post_response = $this->post(route('createPending', ['user' => $this->user1->id]), ['type' => RequestTypesEnum::FRIENDSHIP_REQUEST]);
        $response = $this->delete(route('deletePending', ['request' => $post_response->decodeResponseJson('request_id')]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
