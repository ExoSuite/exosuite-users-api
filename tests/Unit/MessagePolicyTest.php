<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class MessagePolicyTest extends TestCase
{

    /** @var \App\Models\User */
    private $user1;

    /** @var \App\Models\User */
    private $user2;

    /** @var \App\Models\User */
    private $user3;

    public function testModifyGroupMessageWithoutRights(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route('post_group'), ['name' => str_random(100), 'users' => [$this->user2->id]]);
        $group_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route('post_message', [BindType::GROUP => $group_id]),
            ['contents' => str_random(10)]
        );
        $message_id = $response->decodeResponseJson('id');
        Passport::actingAs($this->user3);
        $test = $this->patch(
            $this->route('patch_message', [BindType::GROUP => $group_id, BindType::MESSAGE => $message_id]),
            ['contents' => str_random(10)]
        );
        $test->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteGroupMessageWithoutRights(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route('post_group'), ['name' => str_random(100), 'users' => [$this->user2->id]]);
        $group_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route('post_message', [BindType::GROUP => $group_id]),
            ['contents' => str_random(10)]
        );
        $message_id = $response->decodeResponseJson('id');
        Passport::actingAs($this->user3);
        $test = $this->delete(
            $this->route(
                'delete_message',
                [BindType::GROUP => $group_id, BindType::MESSAGE => $message_id]
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
