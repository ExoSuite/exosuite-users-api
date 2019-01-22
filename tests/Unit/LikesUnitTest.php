<?php

namespace Tests\Unit;

use App\Enums\LikableEntities;
use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webpatser\Uuid\Uuid;

class LikesUnitTest extends TestCase
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
    public function testLikeWithWrongTargetId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('like'), ['liked_id' => Uuid::generate()->string, 'liked_type' => LikableEntities::POST]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testLikeWithWrongType()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('like'), ['liked_id' => Uuid::generate()->string, 'liked_type' => "wrong_type"]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUnlikeWithWrongTargetId()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('unlike', ['entity_id' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetLikesfromLikerWithWrongTargetId()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('getlikesFromLiker', ['user_id' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }}
