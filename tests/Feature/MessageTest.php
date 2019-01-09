<?php

namespace Tests\Feature;

use App\Events\NewMessageEvent;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webpatser\Uuid\Uuid;

/**
 * Class MessageTest
 * @package Tests\Feature
 */
class MessageTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var User
     */
    private $user2;

    public function testCreateMessage()
    {
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $group = factory(Group::class)->create();
        $fake_uuid = $group->id;

        Passport::actingAs($this->user);
        Event::fake([NewMessageEvent::class]);
        $response = $this->post(route("post_message", ["group_id" => $fake_uuid], false), ["contents" => str_random(10)]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Message())->getFillable());
        Event::assertDispatched(NewMessageEvent::class, 1);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }
}
