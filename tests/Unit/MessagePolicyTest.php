<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\Group;
use App\Models\GroupMember;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

class MessagePolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user1;

    /** @var \App\Models\User */
    private $user2;

    /** @var \App\Models\User */
    private $user3;

    public function testModifyGroupMessageWithoutRights(): void
    {
        Passport::actingAs($this->user1);
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(['user_id' => $this->user1->id, 'is_admin' => true]));
        $members->push(new GroupMember(['user_id' => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load('groupMembers');

        $message = factory(Message::class)->create([
            'group_id' => $group['id'],
            'user_id' => $this->user1->id,
        ]);
        Passport::actingAs($this->user3);
        $test = $this->patch(
            $this->route('patch_message', [BindType::GROUP => $group['id'], BindType::MESSAGE => $message['id']]),
            ['contents' => Str::random(10)]
        );
        $test->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteGroupMessageWithoutRights(): void
    {
        Passport::actingAs($this->user1);
        $group = factory(Group::class)->create();
        $members = collect();
        $members->push(new GroupMember(['user_id' => $this->user1->id, 'is_admin' => true]));
        $members->push(new GroupMember(['user_id' => $this->user2->id]));
        $group->groupMembers()->saveMany($members);
        $group->load('groupMembers');

        $message = factory(Message::class)->create([
            'group_id' => $group['id'],
            'user_id' => $this->user1->id,
        ]);
        Passport::actingAs($this->user3);
        $test = $this->delete(
            $this->route(
                'delete_message',
                [BindType::GROUP => $group['id'], BindType::MESSAGE => $message['id']]
            )
        );
        $test->assertStatus(Response::HTTP_FORBIDDEN);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
