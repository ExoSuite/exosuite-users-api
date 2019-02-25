<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class NotificationTest
 * @package Tests\Feature
 */
class NotificationTest extends TestCase
{
    use RefreshDatabase;

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

    public function testGetUserNotifications(): void
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

    public function testDeleteOneUserNotification(): void
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

    public function testDeleteAllReadUserNotification(): void
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

    public function testUpdateOneUserNotification(): void
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

    public function testUpdateAllUserNotification(): void
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
