<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class FollowTest
 *
 * @package Tests\Feature
 */
class FollowTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $user1;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testFollowSomeone(): void
    {
        Passport::actingAs($this->user);
        $follower = new Follow;
        $response = $this->post(route('post_follow', ['user' => $this->user1->id]));
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($follower->getFillable());
        $this->assertDatabaseHas('follows', $response->decodeResponseJson());
    }

    public function testUnfollow(): void
    {
        Passport::actingAs($this->user);
        $follow_response = $this->post(route('post_follow', ['user' => $this->user1->id]));
        $response = $this->delete(route('delete_follow', ['follow' => $follow_response->decodeResponseJson('id')]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('follows', $follow_response->decodeResponseJson());
    }

    public function testGetFollowers(): void
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $user4 = factory(User::class)->create();
        factory(Follow::class)->create(['user_id' => $this->user1->id, 'followed_id' => $this->user->id]);
        factory(Follow::class)->create(['user_id' => $user2->id, 'followed_id' => $this->user->id]);
        factory(Follow::class)->create(['user_id' => $user3->id, 'followed_id' => $this->user->id]);
        factory(Follow::class)->create(['user_id' => $user4->id, 'followed_id' => $this->user->id]);

        Passport::actingAs($this->user);
        $response = $this->get(route('get_followers', ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(4, count($response->decodeResponseJson("data")));
        $response->assertJsonFragment([
            'user_id' => $this->user1->id,
            'first_name' => $this->user1->first_name,
            'last_name' => $this->user1->last_name,
        ]);
    }

    public function testGetMyFollowers(): void
    {
        $user2 = factory(User::class)->create();
        factory(Follow::class)->create(['user_id' => $this->user1->id, 'followed_id' => $this->user->id]);
        factory(Follow::class)->create(['user_id' => $user2->id, 'followed_id' => $this->user->id]);

        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_my_followers'));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, count($response->decodeResponseJson("data")));
        $response->assertJsonFragment([
            'user_id' => $this->user1->id,
            'first_name' => $this->user1->first_name,
            'last_name' => $this->user1->last_name,
        ]);
    }

    public function testGetFollows(): void
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $user4 = factory(User::class)->create();
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $this->user1->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user2->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user3->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user4->id]);

        Passport::actingAs($this->user);
        $response = $this->get(route('get_follows', ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(4, count($response->decodeResponseJson("data")));
        $response->assertJsonFragment([
            'followed_id' => $this->user1->id,
            'first_name' => $this->user1->first_name,
            'last_name' => $this->user1->last_name,
        ]);
    }

    public function testGetMyFollows(): void
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $user4 = factory(User::class)->create();
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $this->user1->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user2->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user3->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user4->id]);

        Passport::actingAs($this->user);
        $response = $this->get(route('get_my_follows'));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(4, count($response->decodeResponseJson("data")));
        $response->assertJsonFragment([
            'followed_id' => $this->user1->id,
            'first_name' => $this->user1->first_name,
            'last_name' => $this->user1->last_name,
        ]);
    }

    public function testAmIFollowing(): void
    {
        Passport::actingAs($this->user1);
        factory(Follow::class)->create(['user_id' => $this->user1->id, 'followed_id' => $this->user->id]);
        $response = $this->get(route('get_am_i_following', ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            'user_id' => $this->user1->id,
            'followed_id' => $this->user->id,
        ]);
    }

    public function testCountFollowers(): void
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $user4 = factory(User::class)->create();
        factory(Follow::class)->create(['user_id' => $this->user1->id, 'followed_id' => $this->user->id]);
        factory(Follow::class)->create(['user_id' => $user2->id, 'followed_id' => $this->user->id]);
        factory(Follow::class)->create(['user_id' => $user3->id, 'followed_id' => $this->user->id]);
        factory(Follow::class)->create(['user_id' => $user4->id, 'followed_id' => $this->user->id]);

        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_followers_number', ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['total' => 4]);
    }

    public function testCountMyFollowers(): void
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $user4 = factory(User::class)->create();
        factory(Follow::class)->create(['user_id' => $this->user1->id, 'followed_id' => $this->user->id]);
        factory(Follow::class)->create(['user_id' => $user2->id, 'followed_id' => $this->user->id]);
        factory(Follow::class)->create(['user_id' => $user3->id, 'followed_id' => $this->user->id]);
        factory(Follow::class)->create(['user_id' => $user4->id, 'followed_id' => $this->user->id]);

        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_my_followers_number'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['total' => 4]);
    }

    public function testCountFollows(): void
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $user4 = factory(User::class)->create();
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $this->user1->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user2->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user3->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user4->id]);

        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_follows_number', ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['total' => 4]);
    }

    public function testCountMyFollows(): void
    {
        $user2 = factory(User::class)->create();
        $user3 = factory(User::class)->create();
        $user4 = factory(User::class)->create();
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $this->user1->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user2->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user3->id]);
        factory(Follow::class)->create(['user_id' => $this->user->id, 'followed_id' => $user4->id]);

        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_my_follows_number'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson(['total' => 4]);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
    }
}
