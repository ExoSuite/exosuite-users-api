<?php

namespace Tests\Feature;

use App\Enums\BindType;
use App\Http\Controllers\GroupController;
use App\Models\User;
use App\Notifications\DeletedGroupNotification;
use App\Notifications\ExpelledFromGroupNotification;
use App\Notifications\NewGroupNotification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    private $user1;

    private $user2;

    private $user3;

    public function testCreateGroupWithName()
    {

        Notification::fake();
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas("groups", array_except($response->decodeResponseJson(), "group_members"));
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        Notification::assertSentTo($this->user2, NewGroupNotification::class);
        Notification::assertNotSentTo($this->user1, NewGroupNotification::class);
    }

    public function testCreateGroupWithoutName()
    {
        Notification::fake();
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["users" => [$this->user2->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertDatabaseHas("groups", array_except($response->decodeResponseJson(), "group_members"));
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        Notification::assertSentTo($this->user2, NewGroupNotification::class);
        Notification::assertNotSentTo($this->user1, NewGroupNotification::class);
    }

    public function testAddNonAdminUserToExistingGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas("groups", array_except($response->decodeResponseJson(), "group_members"));
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "add_user", "user_id" => $this->user3->id]);
        $test_req->assertStatus(Response::HTTP_OK);
    }

    public function testAddAdminUserToExistingGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas("groups", array_except($response->decodeResponseJson(), "group_members"));
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "add_user", "user_id" => $this->user3->id, "is_admin" => true]);
        $test_req->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateToNonAdminUserRightsToExistingGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas("groups", array_except($response->decodeResponseJson(), "group_members"));
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "add_user", "user_id" => $this->user3->id]);
        $test_req->assertStatus(Response::HTTP_OK);
        $req_update_rights = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "update_user_rights", "user_id" => $this->user3->id, "is_admin" => true]);
        $req_update_rights->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateToAdminUserRightsToExistingGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas("groups", array_except($response->decodeResponseJson(), "group_members"));
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "add_user", "user_id" => $this->user3->id, "is_admin" => true]);
        $test_req->assertStatus(Response::HTTP_OK);
        $req_update_rights = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "update_user_rights", "user_id" => $this->user3->id, "is_admin" => false]);
        $req_update_rights->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteUserFromGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id, $this->user3->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas("groups", array_except($response->decodeResponseJson(), "group_members"));
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        $group_id = $response->decodeResponseJson('id');
        Notification::fake();
        $test_req = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "delete_user", "user_id" => $this->user3->id]);
        $test_req->assertStatus(Response::HTTP_OK);
        Notification::assertSentTo($this->user3, ExpelledFromGroupNotification::class);
    }

    public function testUpdateGroupName()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas("groups", array_except($response->decodeResponseJson(), "group_members"));
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        $group_id = $response->decodeResponseJson('id');
        $new_name = "NameForTest";
        $test_req = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "update_group_name", "name" => $new_name]);
        $test_req->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas("groups", $test_req->decodeResponseJson());
    }

    public function testDeleteGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id, $this->user3->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas("groups", array_except($response->decodeResponseJson(), "group_members"));
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        $group_id = $response->decodeResponseJson('id');
        Notification::fake();
        $delete_req = $this->delete($this->route('delete_group', [BindType::GROUP => $group_id]));
        $delete_req->assertStatus(Response::HTTP_NO_CONTENT);
        Notification::assertSentTo($this->user2, DeletedGroupNotification::class);
        Notification::assertSentTo($this->user3, DeletedGroupNotification::class);
        Notification::assertNotSentTo($this->user1, DeletedGroupNotification::class);
        $this->assertDatabaseMissing("groups", array_except($response->decodeResponseJson(), "group_members"));
    }

    protected function setUp()
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
