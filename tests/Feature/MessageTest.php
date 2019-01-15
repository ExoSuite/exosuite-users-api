<?php

namespace Tests\Feature;

use App\Enums\BindType;
use App\Events\ModifyMessageEvent;
use App\Events\NewMessageEvent;
use App\Events\DeletedMessageEvent;
use App\Models\GroupMember;
use App\Models\Group;
use App\Models\Message;
use App\Notifications\Message\NewMessageNotification;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Testing\Fakes\EventFake;
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
        $members = collect();
        $members->push(new GroupMember(["user_id" => $this->user->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        Passport::actingAs($this->user);
        Notification::fake();
        Event::fake([NewMessageEvent::class]);
        $response = $this->post($this->route("post_message", [BindType::GROUP => $group->id]), ["contents" => str_random(10)]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Message())->getFillable());
        $this->assertDatabaseHas("messages", $response->decodeResponseJson());
        Event::assertDispatched(NewMessageEvent::class, 1);
        Notification::assertTimesSent(1, NewMessageNotification::class);
    }

    public function testModifyMessage()
    {
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $this->user->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        Passport::actingAs($this->user);
        $response = $this->post($this->route("post_message", [BindType::GROUP => $group->id]), ["contents" => str_random(10)]);
        $message_id = $response->decodeResponseJson("id");
        Event::fake([ModifyMessageEvent::class]);
        $test = $this->patch($this->route("patch_message", [BindType::GROUP => $group->id, BindType::MESSAGE => $message_id]), ["contents" => str_random(10)]);
        $this->assertTrue($response->decodeResponseJson("contents") !== $test->decodeResponseJson("contents"));
        $test->assertStatus(Response::HTTP_OK);
        $test->assertJsonStructure((new Message())->getFillable());
        $this->assertDatabaseHas("messages", $test->decodeResponseJson());
        Event::assertDispatched(ModifyMessageEvent::class, 1);
    }

    public function testDeleteMessage()
    {
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $this->user->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        Passport::actingAs($this->user);
        $response = $this->post($this->route("post_message", [BindType::GROUP => $group->id]), ["contents" => str_random(10)]);
        $message_id = $response->decodeResponseJson("id");
        Event::fake([DeletedMessageEvent::class]);
        $test = $this->delete($this->route("delete_message", [BindType::GROUP => $group->id, BindType::MESSAGE => $message_id]));
        $this->assertDatabaseMissing("messages", $response->decodeResponseJson());
        $test->assertStatus(Response::HTTP_NO_CONTENT);
        Event::assertDispatched(DeletedMessageEvent::class, 1);
    }

    public function testGetMessages()
    {
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $this->user->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        Passport::actingAs($this->user);
        for ($i = 0; $i < 5; $i++) {
            factory(Message::class)->create(["group_id" => $group->id, "user_id" => $this->user->id]);
        }
        $response = $this->get($this->route("get_message", [BindType::GROUP => $group->id]));
        $this->assertEquals(5, count($response->decodeResponseJson()));
        $response->assertStatus(Response::HTTP_OK);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }
}
