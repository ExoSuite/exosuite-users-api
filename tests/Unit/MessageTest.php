<?php

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

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

    public function testCreateMessageInBadGroup()
    {
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $this->user->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        Passport::actingAs($this->user);
        $response = $this->post($this->route("post_message", [BindType::GROUP => Uuid::generate()->string]), ["contents" => str_random(10)]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testModifyBadMessage()
    {
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $this->user->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        Passport::actingAs($this->user);
        $response = $this->post($this->route("post_message", [BindType::GROUP => $group->id]), ["contents" => str_random(10)]);
        $test = $this->patch($this->route("patch_message", [BindType::GROUP => $group->id, BindType::MESSAGE => Uuid::generate()->string]), ["contents" => str_random(10)]);
        $test->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteBadMessage()
    {
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $this->user->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        Passport::actingAs($this->user);
        $response = $this->post($this->route("post_message", [BindType::GROUP => $group->id]), ["contents" => str_random(10)]);
        $test = $this->delete($this->route("delete_message", [BindType::GROUP => $group->id, BindType::MESSAGE => Uuid::generate()->string]));
        $test->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetBadMessages()
    {
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
        $response = $this->get($this->route("get_message", [BindType::GROUP => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }
}
