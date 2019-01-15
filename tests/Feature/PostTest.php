<?php

namespace Tests\Feature;

use App\Enums\Restriction;
use App\Models\Post;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostTest extends TestCase
{
    private $user;

    private $user1;

    private $dashboard;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dashboard = factory(Dashboard::class)->create();
        $this->dashboard['owner_id'] = $this->user->id;
        $this->dashboard['restriction'] = Restriction::PUBLIC;
        $this->user1 = factory(User::class)->create();

    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testPost()
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('storePost'), ['dashboard_id' => $this->dashboard->id, 'content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Post())->getFillable());
    }

    public function testUpdate()
    {
        Passport::actingAs($this->user);
        $post = factory(Post::class)->create();
        $post['dashboard_id'] = $this->dashboard->id;
        $post['author_id'] = $this->user->id;
        $post['content'] = "";
        $response = $this->patch($this->route('patchPost'), ['id' => $post->id, 'content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDelete()
    {
        Passport::actingAs($this->user);
        $post = factory(Post::class)->create();
        $post['dashboard_id'] = $this->dashboard->id;
        $post['author_id'] = $this->user->id;
        $response = $this->delete($this->route('deletePost'), ['id' => $post->id]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testGetfromDashboard()
    {
        Passport::actingAs($this->user);
        $response = $this->get($this->route('getPosts'), ['dashboard_id' => $this->dashboard->id]);
        $response->assertStatus(Response::HTTP_OK);
    }
}
