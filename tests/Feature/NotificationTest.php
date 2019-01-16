<?php

namespace Tests\Feature;

use App\Enums\BindType;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationTest extends TestCase
{
    private $user1;

    private $user2;

    private $user3;

    public function testGetUserNotifications()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notifs = $notifications_req->decodeResponseJson();
        foreach ($notifs as $notif)
            $this->assertDatabaseHas("notifications", array_except($notif, "data"));
        $notifications_req->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteOneUserNotification()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notification = $notifications_req->decodeResponseJson()[0];
        $notifications_req = $this->delete($this->route("delete_notification", [BindType::NOTIFICATION => $notification['id']]));
        $notifications_req->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing("notifications", array_except($notification, "data"));
    }

    public function testDeleteAllUserNotification()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notifications_array = $notifications_req->decodeResponseJson();
        $notifications_req = $this->delete($this->route("delete_notification"));
        $notifications_req->assertStatus(Response::HTTP_NO_CONTENT);
        foreach ($notifications_array as $notification)
            $this->assertDatabaseMissing("notifications", array_except($notification, "data"));
    }

    public function testUpdateOneUserNotification()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notification = $notifications_req->decodeResponseJson()[0];
        $notifications_req = $this->patch($this->route("patch_notification", [BindType::NOTIFICATION => $notification['id']]));
        $notifications_req->assertStatus(Response::HTTP_OK);
        $notification = $notifications_req->decodeResponseJson();
        $this->assertDatabaseHas("notifications", array_except($notification, "data"));
    }

    public function testUpdateAllUserNotification()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notifications_req = $this->patch($this->route("patch_notification"));
        $notifications_req->assertStatus(Response::HTTP_OK);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
