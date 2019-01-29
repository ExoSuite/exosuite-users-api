<?php

namespace Tests\Feature;

use App\Enums\BindType;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class NotificationTest
 * @package Tests\Feature
 */
class NotificationTest extends TestCase
{


    /**
     * @var
     */
    private $user1;

    /**
     * @var
     */
    private $user2;

    /**
     * @var
     */
    private $user3;

    /**
     *
     */
    public function testGetUserNotifications()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notifs = $notifications_req->decodeResponseJson();
        foreach ($notifs as $notif) {
            $this->assertDatabaseHas("notifications", array_except($notif, "data"));
        }
        $notifications_req->assertStatus(Response::HTTP_OK);
    }

    /**
     *
     */
    public function testDeleteOneUserNotification()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notification = $notifications_req->decodeResponseJson()[0];
        $notifications_req = $this->delete($this->route("delete_notification", [
            BindType::NOTIFICATION => $notification['id']
        ]));
        $notifications_req->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing("notifications", array_except($notification, "data"));
    }

    /**
     *
     */
    public function testDeleteAllReadUserNotification()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notification = $notifications_req->decodeResponseJson()[0];
        $this->patch($this->route("patch_notification", [BindType::NOTIFICATION => $notification['id']]));
        $notifications_req = $this->delete($this->route("delete_notification"));
        $notifications_req->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing("notifications", array_except($notification, "data"));
    }

    /**
     *
     */
    public function testUpdateOneUserNotification()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notification = $notifications_req->decodeResponseJson()[0];
        $notifications_req = $this->patch($this->route("patch_notification", [
            BindType::NOTIFICATION => $notification['id']
        ]));
        $notifications_req->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing("notifications", array_except($notification, "data"));
    }

    /**
     *
     */
    public function testUpdateAllUserNotification()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $this->get($this->route("get_notification"));
        $notifications_req = $this->patch($this->route("patch_notification"));
        $notifications_req->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
