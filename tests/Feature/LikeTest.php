<?php

namespace Tests\Feature;

use App\Enums\LikableEntities;
use App\Models\Dashboard;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class LikeTest extends TestCase
{
    private $user;

    private $dash;

    private $post;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
        $this->post = factory(Post::class)->create(['dashboard_id' => $this->dash->id, 'author_id' => $this->user->id]);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLike()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('like'), ['liked_id' => $this->post->id, 'liked_type' => LikableEntities::POST]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Like())->getFillable());
        $this->assertDatabaseHas('likes', $response->decodeResponseJson());
    }

    public function testUnlike()
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(route('like'), ['liked_id' => $this->post->id, 'liked_type' => LikableEntities::POST]);
        $response = $this->delete(route('unlike', ['entity_id' => $post_resp->decodeResponseJson('liked_id')]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('likes', $post_resp->decodeResponseJson());
    }

    public function testGetLikesFromId()
    {
        $user2 = factory(User::class)->create();
        factory(Like::class)->create(['liked_id' => $this->post->id, 'liked_type' => LikableEntities::POST, 'liker_id' => $this->user->id]);
        factory(Like::class)->create(['liked_id' => $this->post->id, 'liked_type' => LikableEntities::POST, 'liker_id' => $user2->id]);

        Passport::actingAs($this->user);
        $response = $this->get(route('getlikesFromEntity', ['entity_id' => $this->post->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, count($response->decodeResponseJson()));
    }

    public function testGetLikesfromLiker()
    {
        $post2 = factory(Post::class)->create(['dashboard_id' => $this->dash->id, 'author_id' => $this->user->id]);
        factory(Like::class)->create(['liked_id' => $this->post->id, 'liked_type' => LikableEntities::POST, 'liker_id' => $this->user->id]);
        factory(Like::class)->create(['liked_id' => $post2->id, 'liked_type' => LikableEntities::POST, 'liker_id' => $this->user->id]);

        Passport::actingAs($this->user);
        $response = $this->get(route('getlikesFromLiker', ['user_id' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, count($response->decodeResponseJson()));
    }
}
