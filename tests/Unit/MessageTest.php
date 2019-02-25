<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class MessageTest
 * @package Tests\Unit
 */
class MessageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var \App\Models\User
     */
    private $user;

    /**
     * @var \App\Models\User
     */
    private $user2;

    /**
     * @throws \Exception
     */
    public function testCreateMessageInBadGroup(): void
    {
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $this->user->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        Passport::actingAs($this->user);
        $response = $this->post($this->route("post_message", [
            BindType::GROUP => Uuid::generate()->string
        ]), [
            "contents" => str_random(10)
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testModifyBadMessage(): void
    {
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $this->user->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        Passport::actingAs($this->user);
        $response = $this->post($this->route("post_message", [
            BindType::GROUP => $group->id
        ]), [
            "contents" => str_random(10)
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
        $test = $this->patch($this->route("patch_message", [
            BindType::GROUP => $group->id,
            BindType::MESSAGE => Uuid::generate()->string
        ]), [
            "contents" => str_random(10)
        ]);
        $test->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testDeleteBadMessage(): void
    {
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $this->user->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        Passport::actingAs($this->user);
        $response = $this->post($this->route("post_message", [
            BindType::GROUP => $group->id
        ]), [
            "contents" => str_random(10)
        ]);
        $response->assertStatus(Response::HTTP_CREATED);
        $test = $this->delete($this->route("delete_message", [
            BindType::GROUP => $group->id,
            BindType::MESSAGE => Uuid::generate()->string
        ]));
        $test->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testGetBadMessages(): void
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }
}
