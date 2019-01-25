<?php

namespace Tests\Unit;

use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class LikesUnitTest extends TestCase
{
    private $user;

    private $dash;

    private $post;

    private $comm;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
        $this->post = factory(Post::class)->create(['dashboard_id' => $this->dash->id, 'author_id' => $this->user->id]);
        $this->comm = factory(Commentary::class)->create(['post_id' => $this->post->id, 'author_id' => $this->user->id]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLikeWithWrongTargetType()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_like_for_Post', ['user' => $this->user->id, 'dashboard' => $this->dash->id, 'post' => $this->dash->id]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLikeWithWrongTargetId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_like_for_Post', ['user' => $this->user->id, 'dashboard' => $this->dash->id, 'post' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUnlikeWithWrongTargetId()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_like_for_Post', ['user' => $this->user->id, 'dashboard' => $this->dash->id, 'post' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUnlikeWithWrongTargetType()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_like_for_Post', ['user' => $this->user->id, 'dashboard' => $this->dash->id, 'post' => $this->dash->id]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetLikesWithWrongTargetType()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_likes_from_Post', ['user' => $this->user->id, 'dashboard' => $this->dash->id, 'post' => $this->dash->id]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetLikesWithWrongTargetId()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_likes_from_Post', ['user' => $this->user->id, 'dashboard' => $this->dash->id, 'post' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetLikesfromLikerWithWrongTargetId()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_likes_from_liker', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
