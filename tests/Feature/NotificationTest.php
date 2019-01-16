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
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification", [BindType::UUID => $this->user2->id]));
        $notifs = $notifications_req->decodeResponseJson();
        foreach ($notifs as $notif)
            $this->assertDatabaseHas("notifications", array_except($notif, "data"));
        $notifications_req->assertStatus(Response::HTTP_OK);
    }

    /*public function testDeleteUserNotification(){
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification", [BindType::UUID => $this->user2->id]));
        $notification_id = $notifications_req->decodeResponseJson()[0];
        //dd($notification_id);
        $notifications_req = $this->delete($this->route("delete_notification", [BindType::UUID => $this->user2->id, BindType::NOTIFICATION => $notification_id['id']]));
        dd($notifications_req->decodeResponseJson());
        $notifications_req->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function  testUpdateUserNotification(){
        Passport::actingAs($this->user1);
        $response = $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification", [BindType::UUID => $this->user2->id]));
        $notification_id = $notifications_req->decodeResponseJson()[0];
        $notifications_req = $this->patch($this->route("patch_notification", [BindType::UUID => $this->user2->id, BindType::NOTIFICATION => $notification_id['id']]));
        dd($notifications_req->decodeResponseJson());
        $notifications_req->assertStatus(Response::HTTP_NO_CONTENT);
    }*/

    protected function setUp()
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
