<?php

namespace Tests\Feature;

use App\Enums\LikableEntities;
use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Like;
use App\Models\Post;
use App\Models\Run;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LikeTest extends TestCase
{
    /**
     * @var
     */
    private $user;

    /**
     * @var
     */
    private $dash;

    /**
     * @var
     */
    private $post;

    /**
     * @var
     */
    private $comm;

    /**
     * @var
     */
    private $run;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
        $this->post = factory(Post::class)->create([
            'dashboard_id' => $this->dash->id,
            'author_id' => $this->user->id
        ]);
        $this->comm = factory(Commentary::class)->create([
            'post_id' => $this->post->id,
            'author_id' => $this->user->id
        ]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLikeWithPost()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_like_for_Post', [
            'user' => $this->user,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id
        ]));
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Like())->getFillable());
        $this->assertDatabaseHas('likes', $response->decodeResponseJson());
    }

    /**
     *
     */
    public function testLikeWithCommentary()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_like_for_commentary', [
            'user' => $this->user,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id,
            'commentary' => $this->comm->id
        ]));
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Like())->getFillable());
        $this->assertDatabaseHas('likes', $response->decodeResponseJson());
    }

    /**
     *
     */
    public function testUnlikeWithPost()
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('post_like_for_Post', [
            'user' => $this->user,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id
        ]));
        $response = $this->delete(route('delete_like_for_Post', [
            'user' => $this->user,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id
        ]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('likes', $post_resp->decodeResponseJson());
    }

    /**
     *
     */
    public function testUnlikeWithCommentary()
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('post_like_for_commentary', [
            'user' => $this->user,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id,
            'commentary' => $this->comm->id
        ]));
        $response = $this->delete(route('delete_like_for_commentary', [
            'user' => $this->user,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id,
            'commentary' => $this->comm->id
        ]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('likes', $post_resp->decodeResponseJson());
    }

    /**
     *
     */
    public function testGetLikesFromPost()
    {
        $user2 = factory(User::class)->create();
        factory(Like::class)->create([
            'liked_id' => $this->post->id,
            'liked_type' => LikableEntities::POST,
            'liker_id' => $this->user->id
        ]);
        factory(Like::class)->create([
            'liked_id' => $this->post->id,
            'liked_type' => LikableEntities::POST,
            'liker_id' => $user2->id
        ]);

        Passport::actingAs($this->user);
        $response = $this->get(route('get_likes_from_Post', [
            'user' => $this->user,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id
        ]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, count($response->decodeResponseJson()));
    }

    /**
     *
     */
    public function testGetLikesFromCommentary()
    {
        $user2 = factory(User::class)->create();
        factory(Like::class)->create([
            'liked_id' => $this->comm->id,
            'liked_type' => LikableEntities::COMMENTARY,
            'liker_id' => $this->user->id
        ]);
        factory(Like::class)->create([
            'liked_id' => $this->comm->id,
            'liked_type' => LikableEntities::COMMENTARY,
            'liker_id' => $user2->id
        ]);

        Passport::actingAs($this->user);
        $response = $this->get(route('get_likes_from_commentary', [
            'user' => $this->user,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id,
            'commentary' => $this->comm->id
        ]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, count($response->decodeResponseJson()));
    }

    /**
     *
     */
    public function testGetLikesfromLiker()
    {
        $post2 = factory(Post::class)->create([
            'dashboard_id' => $this->dash->id,
            'author_id' => $this->user->id
        ]);
        factory(Like::class)->create([
            'liked_id' => $this->post->id,
            'liked_type' => LikableEntities::POST,
            'liker_id' => $this->user->id
        ]);
        factory(Like::class)->create([
            'liked_id' => $post2->id,
            'liked_type' => LikableEntities::POST,
            'liker_id' => $this->user->id
        ]);

        Passport::actingAs($this->user);
        $response = $this->get(route('get_likes_from_liker', ['user_id' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, count($response->decodeResponseJson()));
    }

    /**
     *
     */
    public function testLikeRun()
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->post($this->route('post_like_for_run', [
            'run_id' => $this->run->id
        ]));
        $response->assertStatus(Response::HTTP_CREATED);
        $this->assertDatabaseHas('likes', $response->decodeResponseJson());
    }

    /**
     *
     */
    public function testUnlikeRun()
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $post_response = $this->post($this->route('post_like_for_run', [
            'run_id' => $this->run->id
        ]));
        $response = $this->delete($this->route('delete_like_for_run', ['run_id' => $this->run->id]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('likes', $post_response->decodeResponseJson());

    }

    /**
     *
     */
    public function testGetLikesFromRun()
    {
        Passport::actingAs($this->user);

        $this->run = factory(Run::class)->create();

        $user2 = factory(User::class)->create();
        factory(Like::class)->create([
            'liked_id' => $this->run->id,
            'liked_type' => LikableEntities::RUN,
            'liker_id' => $this->user->id
        ]);
        factory(Like::class)->create([
            'liked_id' => $this->run->id,
            'liked_type' => LikableEntities::RUN,
            'liker_id' => $user2->id
        ]);

        $response = $this->get($this->route('get_likes_from_run', [
            'run_id' => $this->run->id
        ]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, count($response->decodeResponseJson()));
    }
}
