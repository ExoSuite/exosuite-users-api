<?php

namespace Tests\Unit;

use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class PostsUnitTest
 * @package Tests\Unit
 */
class PostsUnitTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var
     */
    private $user;

    /**
     * @var
     */
    private $user1;

    /**
     * @var
     */
    private $dashboard;

    /**
     * A basic test example.
     *
     * @return void
     * @throws \Exception
     */
    public function testPostOnWrongDashboardId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_Post', [
            'user' => $this->user->id,
            'dashboard' => Uuid::generate()->string
        ]), ['content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testPostOnUnauthorizedDashboard()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id
        ]), [
            'content' => str_random(10)
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not authorized to post on this board."]);
    }

    /**
     * @throws \Exception
     */
    public function testUpdatePostWithWrongId()
    {
        Passport::actingAs($this->user);
        $content = str_random(10);
        $response = $this->patch(route('patch_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
            'post' => Uuid::generate()->string
        ]), ['content' => $content]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testUpdatePostAsUnauthorizedUser()
    {
        Passport::actingAs($this->user1);
        $content = str_random(10);
        $post = factory(Post::class)->create([
            'dashboard_id' => $this->dashboard->id,
            'author_id' => $this->user1->id,
            'content' => str_random(10)
        ]);
        Passport::actingAs($this->user);
        $response = $this->patch(route('patch_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
            'post' => $post->id
        ]), ['content' => $content]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to update this post."]);
    }

    /**
     * @throws \Exception
     */
    public function testGetPostsWithWrongId()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_Posts_by_dashboard_id', [
            'user' => $this->user->id,
            'dashboard' => Uuid::generate()->string
        ]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testGetPostAsUnauthorizedUser()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_Posts_by_dashboard_id', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id
        ]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to access this dashboard."]);
    }

    /**
     * @throws \Exception
     */
    public function testDeletePostWithWrongId()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
            'post_id' => Uuid::generate()->string
        ]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testDeletePostAsUnauthorizedUser()
    {
        $post = factory(Post::class)->create([
            'dashboard_id' => $this->dashboard->id,
            'author_id' => $this->user1->id,
            'content' => str_random(10)
        ]);
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
            'post_id' => $post->id
        ]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to delete this post."]);
    }

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
        $this->dashboard = factory(Dashboard::class)->create(['owner_id' => $this->user1->id]);
    }
}
