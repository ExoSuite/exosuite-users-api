<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\TokenScope;
use App\Events\DeletedMessageEvent;
use App\Events\ModifyMessageEvent;
use App\Events\NewMessageEvent;
use App\Http\Controllers\MessageController;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Message;
use App\Models\User;
use App\Notifications\Message\NewMessageNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class MessageTest
 *
 * @package Tests\Feature
 */
class MessageTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $user2;

    /** @var string[] */
    private $expectedJsonStructure;

    public function testCreateMessage(): void
    {
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(['user_id' => $this->user->id, 'is_admin' => true]));
        $members->push(new GroupMember(['user_id' => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load('groupMembers');

        Passport::actingAs($this->user, [TokenScope::MESSAGE]);
        Notification::fake();
        Event::fake([NewMessageEvent::class]);
        $response = $this->post(
            $this->route('post_message', [BindType::GROUP => $group->id]),
            ['contents' => Str::random(10)]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($this->expectedJsonStructure);
        $this->assertDatabaseHas('messages', Arr::except($response->decodeResponseJson(), "user"));
        $self = $this;
        Event::assertDispatched(
            NewMessageEvent::class,
            static function (NewMessageEvent $message) use ($response, $self): bool {
                $self->assertEquals($message->broadcastWith(), $response->decodeResponseJson());

                return true;
            }
        );
        Notification::assertTimesSent(1, NewMessageNotification::class);
    }

    public function testModifyMessage(): void
    {
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(['user_id' => $this->user->id, 'is_admin' => true]));
        $members->push(new GroupMember(['user_id' => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load('groupMembers');

        Passport::actingAs($this->user, [TokenScope::MESSAGE]);
        $message = factory(Message::class)->create([
            'group_id' => $group['id'],
            'user_id' => $this->user->id,
        ]);
        Event::fake([ModifyMessageEvent::class]);
        $patch = $this->patch(
            $this->route('patch_message', [BindType::GROUP => $group->id, BindType::MESSAGE => $message['id']]),
            ['contents' => Str::random(10)]
        );
        $patch->assertStatus(Response::HTTP_OK);
        $this->assertTrue($message['contents'] !== $patch->decodeResponseJson('contents'));
        $this->assertDatabaseHas('messages', Arr::except($patch->decodeResponseJson(), "user"));
        $self = $this;
        Event::assertDispatched(
            ModifyMessageEvent::class,
            static function (ModifyMessageEvent $message) use ($patch, $self): bool {
                $self->assertEquals($message->broadcastWith(), $patch->decodeResponseJson());

                return true;
            }
        );
        $patch->assertJsonStructure($this->expectedJsonStructure);
    }

    public function testDeleteMessage(): void
    {
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(['user_id' => $this->user->id, 'is_admin' => true]));
        $members->push(new GroupMember(['user_id' => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load('groupMembers');

        Passport::actingAs($this->user, [TokenScope::MESSAGE]);
        $response = $this->post(
            $this->route('post_message', [BindType::GROUP => $group->id]),
            ['contents' => Str::random(10)]
        );
        $message_id = $response->decodeResponseJson('id');
        Event::fake([DeletedMessageEvent::class]);
        $delete = $this->delete(
            $this->route('delete_message', [BindType::GROUP => $group->id, BindType::MESSAGE => $message_id])
        );
        $delete->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseMissing('messages', Arr::except($delete->decodeResponseJson(), "user"));
        Event::assertDispatched(
            DeletedMessageEvent::class,
            static function (DeletedMessageEvent $message): bool {
                return array_key_exists("id", $message->broadcastWith());
            }
        );
        $delete->assertJsonStructure($this->expectedJsonStructure);
    }

    public function testGetMessages(): void
    {
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(['user_id' => $this->user->id, 'is_admin' => true]));
        $members->push(new GroupMember(['user_id' => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load('groupMembers');

        Passport::actingAs($this->user, [TokenScope::MESSAGE]);

        for ($i = 0; $i < 31; $i++) {
            factory(Message::class)->create([
                'group_id' => $group->id,
                'user_id' => $this->user->id,
                'contents' => $i,
            ]);
        }

        $response = $this->get($this->route('get_message', [BindType::GROUP => $group->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(MessageController::GET_PER_PAGE, count($response->decodeResponseJson('data')));
        $response->assertJsonStructure(
            $this->expectedJsonStructure,
            $response->decodeResponseJson("data")[0]
        );
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->expectedJsonStructure = array_merge(
            ["user" => Message::getPublicUserFields()],
            (new Message)->getFillable()
        );
    }
}
