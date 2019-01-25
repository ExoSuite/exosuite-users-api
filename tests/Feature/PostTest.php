<?php

namespace Tests\Feature;

use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PostTest extends TestCase
{
    private $user;

    private $user1;

    private $dashboard;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dashboard = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
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
        $response = $this->post(route('post_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id
        ]), ['content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Post())->getFillable());
        $this->assertDatabaseHas('posts', $response->decodeResponseJson());
    }

    public function testUpdate()
    {
        Passport::actingAs($this->user);
        $content = str_random(10);
        $post = factory(Post::class)->create([
            'dashboard_id' => $this->dashboard->id,
            'author_id' => $this->user->id,
            'content' => str_random(10)
        ]);
        $response = $this->patch(route('patch_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
            'post' => $post->id
        ]), ['content' => $content]);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'author_id' => $this->user->id, 'content' => $content]);
    }

    public function testDelete()
    {
        Passport::actingAs($this->user);
        $post_response = $this->post(route('post_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id]), ['content' => str_random(10)]);
        $response = $this->delete(route('delete_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
            'post' => $post_response->decodeResponseJson('id')
        ]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('posts', $post_response->decodeResponseJson());
    }

    public function testGetfromDashboard()
    {
        Passport::actingAs($this->user);
        for ($i = 0; $i < 5; $i++) {
            factory(Post::class)->create([
                'dashboard_id' => $this->dashboard->id,
                'author_id' => $this->user->id,
                'content' => str_random(10)
            ]);
        }
        $response = $this->get(route('get_Posts_by_dashboard_id', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id
        ]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(5, count($response->decodeResponseJson()));
    }
}
