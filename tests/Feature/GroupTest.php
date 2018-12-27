<?php

namespace Tests\Feature;

use App\Http\Controllers\GroupController;
use App\Models\User;
use App\Notifications\NewGroupNotification;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Notification;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    public function testCreateGroupWithName()
    {
        Notification::fake();
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        Passport::actingAs($user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(255), "users" => [$user2->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        Notification::assertSentTo($user2, NewGroupNotification::class);
        Notification::assertNotSentTo($user1, NewGroupNotification::class);
    }

    public function testCreateGroupWithoutName()
    {
        Notification::fake();
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        Passport::actingAs($user1);
        $response = $this->post($this->route("post_group"), ["users" => [$user2->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(["name", "id", "updated_at", "created_at", "group_members"]);
        $this->assertTrue(is_array($response->decodeResponseJson("group_members")));
        Notification::assertSentTo($user2, NewGroupNotification::class);
        Notification::assertNotSentTo($user1, NewGroupNotification::class);
    }
}
