<?php

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webpatser\Uuid\Uuid;

class NotificationTest extends TestCase
{

    private $user1;

    private $user2;

    private $user3;

    public function testDeleteOneBadUserNotification()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notifications_req = $this->delete($this->route("delete_notification", [BindType::NOTIFICATION => Uuid::generate()->string]));
        $notifications_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateOneBadUserNotification()
    {
        Passport::actingAs($this->user1);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user3);
        $this->post($this->route("post_group"), ["name" => str_random(100), "users" => [$this->user2->id]]);
        Passport::actingAs($this->user2);
        $notifications_req = $this->get($this->route("get_notification"));
        $notifications_req = $this->patch($this->route("patch_notification", [BindType::NOTIFICATION => Uuid::generate()->string]));
        $notifications_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
