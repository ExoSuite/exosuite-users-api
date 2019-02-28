<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class NotificationTest
 *
 * @package Tests\Unit
 */
class NotificationTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user1;

    /** @var \App\Models\User */
    private $user2;

    /** @var \App\Models\User */
    private $user3;

    /**
     * @throws \Exception
     */
    public function testDeleteOneBadUserNotification(): void
    {
        Passport::actingAs($this->user1);
        $this->post($this->route('post_group'), [
            'name' => str_random(100),
            'users' => [$this->user2->id],
        ]);
        Passport::actingAs($this->user3);
        $this->post($this->route('post_group'), [
            'name' => str_random(100),
            'users' => [$this->user2->id],
        ]);
        Passport::actingAs($this->user2);
        $this->get($this->route('get_notification'));
        $notifications_req = $this->delete($this->route('delete_notification', [
            BindType::NOTIFICATION => Uuid::generate()->string,
        ]));
        $notifications_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateOneBadUserNotification(): void
    {
        Passport::actingAs($this->user1);
        $this->post($this->route('post_group'), [
            'name' => str_random(100),
            'users' => [$this->user2->id],
        ]);
        Passport::actingAs($this->user3);
        $this->post($this->route('post_group'), [
            'name' => str_random(100),
            'users' => [$this->user2->id],
        ]);
        Passport::actingAs($this->user2);
        $this->get($this->route('get_notification'));
        $notifications_req = $this->patch($this->route('patch_notification', [
            BindType::NOTIFICATION => Uuid::generate()->string,
        ]));
        $notifications_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
