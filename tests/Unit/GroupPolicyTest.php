<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class GroupPolicyTest
 *
 * @package Tests\Unit
 */
class GroupPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user1;

    /** @var \App\Models\User */
    private $user2;

    /** @var \App\Models\User */
    private $user3;

    public function testPatchGroupWithoutAdminRights(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id]]
        );
        $group_id = $response->decodeResponseJson('id');
        $new_name = 'NameForTest';
        Passport::actingAs($this->user2);
        $test_req = $this->patch(
            $this->route('patch_group', [BindType::GROUP => $group_id]),
            ['request_type' => 'update_group_name', 'name' => $new_name]
        );
        $test_req->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testDeleteGroupWithoutAdminRights(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id]]
        );
        $group_id = $response->decodeResponseJson('id');
        Passport::actingAs($this->user2);
        $delete_req = $this->delete($this->route('delete_group', [BindType::GROUP => $group_id]));
        $delete_req->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testPostGroupMessageWithoutRights(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id]]
        );
        $group_id = $response->decodeResponseJson('id');
        Passport::actingAs($this->user3);
        $response = $this->post(
            $this->route('post_message', [BindType::GROUP => $group_id]),
            ['contents' => Str::random(10)]
        );
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function testViewGroupMessagesWithoutRights(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id]]
        );
        $group_id = $response->decodeResponseJson('id');
        Passport::actingAs($this->user3);
        $response = $this->get($this->route('get_message', [BindType::GROUP => $group_id]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
