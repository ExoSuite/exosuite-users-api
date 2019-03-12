<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Models\User;
use App\Notifications\DeletedGroupNotification;
use App\Notifications\ExpelledFromGroupNotification;
use App\Notifications\NewGroupNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class GroupTest
 *
 * @package Tests\Feature
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

    public function testCreateGroupWithName(): void
    {
        Notification::fake();
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
        $response->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($response->decodeResponseJson('group_members')));
        Notification::assertSentTo($this->user2, NewGroupNotification::class);
        Notification::assertNotSentTo($this->user1, NewGroupNotification::class);
    }

    public function testCreateGroupWithoutName(): void
    {
        Notification::fake();
        Passport::actingAs($this->user1);
        $response = $this->post($this->route('post_group'), ['users' => [$this->user2->id]]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertDatabaseHas('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
        $this->assertTrue(is_array($response->decodeResponseJson('group_members')));
        Notification::assertSentTo($this->user2, NewGroupNotification::class);
        Notification::assertNotSentTo($this->user1, NewGroupNotification::class);
    }

    public function testAddNonAdminUserToExistingGroup(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
        $response->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($response->decodeResponseJson('group_members')));
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch(
            $this->route('patch_group', [BindType::GROUP => $group_id]),
            ['request_type' => 'add_user', 'user_id' => $this->user3->id]
        );
        $test_req->assertStatus(Response::HTTP_OK);
    }

    public function testAddAdminUserToExistingGroup(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
        $response->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($response->decodeResponseJson('group_members')));
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch(
            $this->route('patch_group', [BindType::GROUP => $group_id]),
            ['request_type' => 'add_user', 'user_id' => $this->user3->id, 'is_admin' => true]
        );
        $test_req->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateToNonAdminUserRightsToExistingGroup(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
        $response->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($response->decodeResponseJson('group_members')));
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch(
            $this->route('patch_group', [BindType::GROUP => $group_id]),
            ['request_type' => 'add_user', 'user_id' => $this->user3->id]
        );
        $test_req->assertStatus(Response::HTTP_OK);
        $req_update_rights = $this->patch(
            $this->route('patch_group', [BindType::GROUP => $group_id]),
            ['request_type' => 'update_user_rights', 'user_id' => $this->user3->id, 'is_admin' => true]
        );
        $req_update_rights->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateToAdminUserRightsToExistingGroup(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
        $response->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($response->decodeResponseJson('group_members')));
        $group_id = $response->decodeResponseJson('id');
        $test_req = $this->patch(
            $this->route('patch_group', [BindType::GROUP => $group_id]),
            ['request_type' => 'add_user', 'user_id' => $this->user3->id, 'is_admin' => true]
        );
        $test_req->assertStatus(Response::HTTP_OK);
        $req_update_rights = $this->patch(
            $this->route('patch_group', [BindType::GROUP => $group_id]),
            ['request_type' => 'update_user_rights', 'user_id' => $this->user3->id, 'is_admin' => false]
        );
        $req_update_rights->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteUserFromGroup(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id, $this->user3->id]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
        $response->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($response->decodeResponseJson('group_members')));
        $group_id = $response->decodeResponseJson('id');
        Notification::fake();
        $test_req = $this->patch(
            $this->route('patch_group', [BindType::GROUP => $group_id]),
            ['request_type' => 'delete_user', 'user_id' => $this->user3->id]
        );
        $test_req->assertStatus(Response::HTTP_OK);
        Notification::assertSentTo($this->user3, ExpelledFromGroupNotification::class);
    }

    public function testUpdateGroupName(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
        $response->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($response->decodeResponseJson('group_members')));
        $group_id = $response->decodeResponseJson('id');
        $new_name = 'NameForTest';
        $test_req = $this->patch(
            $this->route('patch_group', [BindType::GROUP => $group_id]),
            ['request_type' => 'update_group_name', 'name' => $new_name]
        );
        $test_req->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($test_req->decodeResponseJson('group_members')));
    }

    public function testDeleteGroup(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id, $this->user3->id]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
        $response->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($response->decodeResponseJson('group_members')));
        $group_id = $response->decodeResponseJson('id');
        Notification::fake();
        $delete_req = $this->delete($this->route('delete_group', [BindType::GROUP => $group_id]));
        $delete_req->assertStatus(Response::HTTP_NO_CONTENT);
        Notification::assertSentTo($this->user2, DeletedGroupNotification::class);
        Notification::assertSentTo($this->user3, DeletedGroupNotification::class);
        Notification::assertNotSentTo($this->user1, DeletedGroupNotification::class);
        $this->assertDatabaseMissing('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
    }

    public function testGetGroup(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(
            $this->route('post_group'),
            ['name' => Str::random(100), 'users' => [$this->user2->id, $this->user3->id]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('groups', Arr::except($response->decodeResponseJson(), 'group_members'));
        $response->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($response->decodeResponseJson('group_members')));
        $group_id = $response->decodeResponseJson('id');

        // GET REQUEST
        $get_req = $this->get($this->route('get_group', [BindType::GROUP => $group_id]));
        $get_req->assertStatus(Response::HTTP_OK);
        $get_req->assertJsonStructure(['name', 'id', 'updated_at', 'created_at', 'group_members']);
        $this->assertTrue(is_array($get_req->decodeResponseJson('group_members')));
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $this->user3 = factory(User::class)->create();
    }
}
