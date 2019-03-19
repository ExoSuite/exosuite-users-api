<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class PostTest
 *
 * @package Tests\Feature
 */
class PostTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\Dashboard */
    private $dashboard;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreatePost(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_Post', [
            'user' => $this->user->id,
        ]), ['content' => Str::random(10)]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Post)->getFillable());
        $this->assertDatabaseHas('posts', $response->decodeResponseJson());
    }

    public function testUpdate(): void
    {
        Passport::actingAs($this->user);
        $content = Str::random(10);
        $post = factory(Post::class)->create([
            'dashboard_id' => $this->dashboard->id,
            'author_id' => $this->user->id,
            'content' => Str::random(10),
        ]);
        $response = $this->patch(route('patch_Post', [
            'user' => $this->user->id,
            'post' => $post->id,
        ]), ['content' => $content]);
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas('posts', ['id' => $post->id, 'author_id' => $this->user->id, 'content' => $content]);
    }

    public function testDelete(): void
    {
        Passport::actingAs($this->user);
        $postResponse = $this->post(route('post_Post', [
            'user' => $this->user->id,
        ]), ['content' => Str::random(10)]);
        $response = $this->delete(route('delete_Post', [
            'user' => $this->user->id,
            'post' => $postResponse->decodeResponseJson('id'),
        ]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('posts', $postResponse->decodeResponseJson());
    }

    public function testGetPostsFromDashboard(): void
    {
        Passport::actingAs($this->user);

        for ($i = 0; $i < 5; $i++) {
            factory(Post::class)->create([
                'dashboard_id' => $this->dashboard->id,
                'author_id' => $this->user->id,
                'content' => Str::random(10),
            ]);
        }

        $response = $this->get(route('get_Posts_from_dashboard', [
            'user' => $this->user->id,
        ]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(5, count($response->decodeResponseJson("data")));
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dashboard = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
    }
}
