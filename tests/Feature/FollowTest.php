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
        $response = $this->post(route("post_follow", ["user" => $this->user1->id]));
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($follower->getFillable());
        $this->assertDatabaseHas('follows', $response->decodeResponseJson());
    }

    public function testUnfollow(): void
    {
        Passport::actingAs($this->user);
        $follow_response = $this->post(route("post_follow", ["user" => $this->user1->id]));
        $response = $this->delete(route('delete_follow', ["user" => $this->user1->id]));
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
        $response = $this->get(route("get_followers", ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(4, count($response->decodeResponseJson()));
    }

    public function testAmIFollowing(): void
    {
        Passport::actingAs($this->user);
        factory(Follow::class)->create(['user_id' => $this->user1->id, 'followed_id' => $this->user->id]);
        $response = $this->get(route("get_am_i_following", ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
    }
}
