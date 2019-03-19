<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class GroupTest
 *
 * @package Tests\Unit
 */
class GroupTest extends TestCase
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
    public function testCreateBadGroupWithName(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route('post_group'), [
            'name' => Str::random(100),
            'users' => Uuid::generate()->string,
        ]);
        $response->assertJsonValidationErrors(['users']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testCreateBadGroupWithoutName(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route('post_group'), ['users' => Uuid::generate()->string]);
        $response->assertJsonValidationErrors(['users']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testAddBadNonAdminUserToExistingGroup(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route('post_group'), [
            'name' => Str::random(100),
            'users' => [$this->user2->id],
        ]);
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch($this->route('patch_group', [
            BindType::GROUP => $group_id,
        ]), [
            'request_type' => 'add_user',
            'user_id' => Uuid::generate()->string,
        ]);
        $test_req->assertJsonValidationErrors(['user_id']);
        $test_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testAddBadAdminUserToExistingGroup(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route('post_group'), [
            'name' => Str::random(100),
            'users' => [$this->user2->id],
        ]);
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch($this->route('patch_group', [
            BindType::GROUP => $group_id,
        ]), [
            'request_type' => 'add_user',
            'user_id' => Uuid::generate()->string,
            'is_admin' => true,
        ]);
        $test_req->assertJsonValidationErrors(['user_id']);
        $test_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateGroupWithoutRights(): void
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

    /**
     * @throws \Exception
     */
    public function testDeleteBadUserFromGroup(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post($this->route('post_group'), [
            'name' => Str::random(100),
            'users' => [$this->user2->id, $this->user3->id],
        ]);
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch($this->route('patch_group', [
            BindType::GROUP => $group_id,
        ]), [
            'request_type' => 'delete_user',
            'user_id' => Uuid::generate()->string,
        ]);
        $test_req->assertJsonValidationErrors(['user_id']);
        $test_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testDeleteBadGroup(): void
    {
        Passport::actingAs($this->user1);
        $this->post($this->route('post_group'), [
            'name' => Str::random(100),
            'users' => [$this->user2->id, $this->user3->id],
        ]);
        $delete_req = $this->delete($this->route('delete_group', [BindType::GROUP => Uuid::generate()->string]));
        $delete_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    public function testDeleteGroupWithoutRights(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id, $this->user3->id]]
        );
        $group_id = $response->decodeResponseJson('id');
        Passport::actingAs($this->user2);
        $delete_req = $this->delete($this->route('delete_group', [BindType::GROUP => $group_id]));
        $delete_req->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws \Exception
     */
    public function testGetBadGroup(): void
    {
        Passport::actingAs($this->user1);
        $this->post($this->route('post_group'), [
            'name' => Str::random(100),
            'users' => [$this->user2->id, $this->user3->id],
        ]);
        $get_req = $this->get($this->route('get_group', [BindType::GROUP => Uuid::generate()->string]));
        $get_req->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
