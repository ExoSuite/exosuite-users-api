<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class PostsUnitTest
 *
 * @package Tests\Unit
 */
class PostsUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $user1;

    /** @var \App\Models\User */
    private $dashboard;

    /**
     * A basic test example.
     *
     * @return void
     * @throws \Exception
     */
    public function testPostOnWrongDashboardId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_Post', [
            'user' => $this->user->id,
            'dashboard' => Uuid::generate()->string,
        ]), ['content' => Str::random()]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testPostOnUnauthorizedDashboard(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
        ]), [
            'content' => Str::random(),
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws \Exception
     */
    public function testUpdatePostWithWrongId(): void
    {
        Passport::actingAs($this->user);
        $content = Str::random();
        $response = $this->patch(route('patch_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
            'post' => Uuid::generate()->string,
        ]), ['content' => $content]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdatePostAsUnauthorizedUser(): void
    {
        Passport::actingAs($this->user1);
        $post = factory(Post::class)->create([
            'dashboard_id' => $this->dashboard->id,
            'author_id' => $this->user1->id,
            'content' => Str::random(),
        ]);
        Passport::actingAs($this->user);
        $response = $this->patch(route('patch_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
            'post' => $post->id,
        ]), ['content' => Str::random()]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws \Exception
     */
    public function testGetPostsWithWrongId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_Posts_by_dashboard_id', [
            'user' => $this->user->id,
            'dashboard' => Uuid::generate()->string,
        ]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetPostAsUnauthorizedUser(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_Posts_by_dashboard_id', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
        ]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws \Exception
     */
    public function testDeletePostWithWrongId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
            'post_id' => Uuid::generate()->string,
        ]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeletePostAsUnauthorizedUser(): void
    {
        $post = factory(Post::class)->create([
            'dashboard_id' => $this->dashboard->id,
            'author_id' => $this->user1->id,
            'content' => Str::random(),
        ]);
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dashboard->id,
            'post_id' => $post->id,
        ]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
        $this->dashboard = factory(Dashboard::class)->create(['owner_id' => $this->user1->id]);
    }
}
