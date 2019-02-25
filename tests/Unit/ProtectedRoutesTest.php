<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\User;
use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class ProtectedRoutesTest
 * @package Tests\Unit
 */
class ProtectedRoutesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAuthException(): void
    {
        Artisan::call('passport:install');
        $response = $this->json(Request::METHOD_GET, route('get_user'));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testPatchGroupWithoutBeingLogged(): void
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $user1->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        $test_req = $this->patch($this->route("patch_group", [BindType::GROUP => $group->id]), ["request_type" => "update_user_rights", "user_id" => $user2->id, "is_admin" => true]);
        $test_req->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testCreateMessageWithoutBeingLogged(): void
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(["user_id" => $user1->id, "is_admin" => true]));
        $members->push(new GroupMember(["user_id" => $user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load("groupMembers");

        $response = $this->post($this->route("post_message", [BindType::GROUP => $group->id]), ["contents" => str_random(10)]);
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testGetNotificationsWithoutBeingLogged(): void
    {
        $response = $this->get($this->route("get_notification"));
        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }
}
