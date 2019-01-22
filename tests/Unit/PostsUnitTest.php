<?php

namespace Tests\Unit;

use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class PostsUnitTest extends TestCase
{
    private $user;

    private $user1;

    private $dashboard;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
        $this->dashboard = factory(Dashboard::class)->create(['owner_id' => $this->user1->id]);
    }
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPostOnWrongDashboardId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('storePost'), ['dashboard_id' => Uuid::generate()->string, 'content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testPostOnUnauthorizedDashboard()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('storePost'), ['dashboard_id' => $this->dashboard->id, 'content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not authorized to post on this board."]);
    }

    public function testUpdatePostWithWrongId()
    {
        Passport::actingAs($this->user);
        $content = str_random(10);
        $response = $this->patch(route('patchPost'), ['id' => Uuid::generate()->string, 'content' => $content]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdatePostAsUnauthorizedUser()
    {
        Passport::actingAs($this->user1);
        $content = str_random(10);
        $post = factory(Post::class)->create(['dashboard_id' => $this->dashboard->id, 'author_id' => $this->user1->id, 'content' => str_random(10)]);
        Passport::actingAs($this->user);
        $response = $this->patch(route('patchPost'), ['id' => $post->id, 'content' => $content]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to update this post."]);
    }

    public function testGetPostsWithWrongId()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('getPosts', ['dashboard_id' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetPostAsUnauthorizedUser()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('getPosts', ['dashboard_id' => $this->dashboard->id]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to access this dashboard."]);

    }

    public function testDeletePostWithWrongId()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('deletePost', ['post_id' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeletePostAsUnauthorizedUser()
    {
        $post = factory(Post::class)->create(['dashboard_id' => $this->dashboard->id, 'author_id' => $this->user1->id, 'content' => str_random(10)]);
        Passport::actingAs($this->user);
        $response = $this->delete(route('deletePost', ['post_id' => $post->id]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to delete this post."]);
    }
}
