<?php

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class GroupTest extends TestCase
{
    private $user1;

    private $user2;

    private $user3;

    public function testCreateBadGroupWithName()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => Uuid::generate()->string]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateBadGroupWithoutName()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["users" => Uuid::generate()->string]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testAddBadNonAdminUserToExistingGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "add_user", "user_id" => Uuid::generate()->string]);
        $test_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testAddBadAdminUserToExistingGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "add_user", "user_id" => Uuid::generate()->string, "is_admin" => true]);
        $test_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteBadUserFromGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id, $this->user3->id]]);
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch($this->route("patch_group", [BindType::GROUP => $group_id]), ["request_type" => "delete_user", "user_id" => Uuid::generate()->string]);
        $test_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteBadGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id, $this->user3->id]]);
        $delete_req = $this->delete($this->route('delete_group', [BindType::GROUP => Uuid::generate()->string]));
        $delete_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetBadGroup()
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id, $this->user3->id]]);
        $get_req = $this->get($this->route('get_group', [BindType::GROUP => Uuid::generate()->string]));
        $get_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
