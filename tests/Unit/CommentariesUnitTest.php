<?php

namespace Tests\Unit;

use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webpatser\Uuid\Uuid;

class CommentariesUnitTest extends TestCase
{
    private $user;

    private $user1;

    private $dash;

    private $post;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
        $this->post = factory(Post::class)
            ->create(['author_id' => $this->user->id, 'dashboard_id' => $this->dash->id, 'content' => str_random(10)]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateCommsOnFalsePostId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('storeCommentary'), ["post_id" => Uuid::generate()->string, 'content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateCommsAsUnauthorizedUser()
    {
        Passport::actingAs($this->user1);
        $response = $this->post(route('storeCommentary'), ["post_id" => $this->post->id, 'content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to post a commentary on this post"]);
    }

    public function testGetCommsOnFalsePostId()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('getComms', ['post_id' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetCommsAsUnauthorizedUser()
    {
        Passport::actingAs($this->user1);
        $response = $this->get(route('getComms', ['post_id' => $this->post->id]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to access this post."]);

    }

    public function testUpdateCommOnFalseCommId()
    {
        Passport::actingAs($this->user);
        $content = str_random(10);
        $response = $this->patch(route('updateCommentary'), ['id' => Uuid::generate()->string, 'content' => $content]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateCommAsUnauthorizedUser()
    {
        Passport::actingAs($this->user1);
        $content = str_random(10);
        $comm = factory(Commentary::class)->create(['post_id' => $this->post->id, 'author_id' => $this->user->id, 'content' => str_random(10)]);
        $response = $this->patch(route('updateCommentary'), ['id' => $comm->id, 'content' => $content]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allow to modify this commentary."]);

    }

    public function testDeleteCommOnFalseCommId()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('deleteCommentary', ['commentary_id' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    }

    public function testDeleteCommAsUnauthorizedUser()
    {
        Passport::actingAs($this->user1);
        $comm = factory(Commentary::class)->create(['post_id' => $this->post->id, 'author_id' => $this->user->id, 'content' => str_random(10)]);
        $response = $this->delete(route('deleteCommentary', ['commentary_id' => $comm->id]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to delete this post."]);

    }
}
