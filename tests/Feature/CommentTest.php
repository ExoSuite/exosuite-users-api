<?php

namespace Tests\Feature;

use App\Enums\Restriction;
use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommentTest extends TestCase
{
    private $user;

    private $dash;

    private $post;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create();
        $this->dash['owner_id'] = $this->user->id;
        $this->dash['restriction'] = Restriction::PUBLIC;
        $this->post = factory(Post::class)->create();
        $this->post['author_id'] = $this->user->id;
        $this->post['dashboard_id'] = $this->dash->id;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreate()
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('storeCommentary'), ["post_id" => $this->post->id, 'content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Commentary())->getFillable());
    }

    public function testGetComms()
    {
        Passport::actingAs($this->user);
        $response = $this->get($this->route('getComms'), ['id' => $this->post->id]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateComm()
    {
        Passport::actingAs($this->user);
        $comm = factory(Commentary::class)->create();
        $comm['post_id'] = $this->post->id;
        $comm['content'] = "";
        $response = $this->patch($this->route('updateCommentary'), ['id' => $comm->id, 'content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteComm()
    {
        Passport::actingAs($this->user);
        $comm = factory(Commentary::class)->create();
        $comm['post_id'] = $this->post->id;
        $response = $this->delete($this->route('deleteCommentary'), ['id' => $comm->id]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
