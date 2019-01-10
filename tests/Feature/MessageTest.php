<?php

namespace Tests\Feature;

use App\Events\ModifyMessageEvent;
use App\Events\NewMessageEvent;
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
        $response = $this->post($this->route("post_message", ["group_id" => $group->id]), ["contents" => str_random(10)]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Message())->getFillable());
        Event::assertDispatched(NewMessageEvent::class, 1);
        Notification::assertTimesSent(1, NewMessageNotification::class);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }
}
