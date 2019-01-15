<?php

namespace Tests\Feature;

use App\Enums\LikableEntities;
use App\Enums\Restriction;
use App\Models\Dashboard;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LikeTest extends TestCase
{
    private $user;

    private $dash;

    private $post;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create();
        $this->dash['restriction'] = Restriction::PUBLIC;
        $this->dash['owner_id'] = $this->user->id;
        $this->post = factory(Post::class)->create();
        $this->post['dashboard_id'] = $this->dash->id;
        $this->post['author_id'] = $this->user->id;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLike()
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('like'), ['liked_id' => $this->post->id, 'liked_type' => LikableEntities::POST]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Like())->getFillable());
    }

    public function testUnlike()
    {
        Passport::actingAs($this->user);
        $like = factory(Like::class)->create();
        $like['liked_id'] = $this->post->id;
        $like['liked_type'] = LikableEntities::POST;
        $like['liker_id'] = $this->user->id;
        $response = $this->delete($this->route('unlike'), ['liked_id' => $this->post->id]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testGetLikesFromId()
    {
        Passport::actingAs($this->user);
        $response = $this->get($this->route('getlikesId'), ['liked_id' => $this->post->id]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testGetLikesfromLiker()
    {
        Passport::actingAs($this->user);
        $response = $this->get($this->route('getlikesLiker'), ['liker_id' => $this->user->id]);
        $response->assertStatus(Response::HTTP_OK);
    }
}
