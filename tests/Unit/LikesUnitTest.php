<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class LikesUnitTest
 *
 * @package Tests\Unit
 */
class LikesUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\Dashboard */
    private $dash;

    /** @var \App\Models\Post */
    private $post;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLikeWithWrongTargetType(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            route('post_like_for_Post', [
                'user' => $this->user->id,
                'dashboard' => $this->dash->id,
                'post' => $this->dash->id,
            ])
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testLikeWithWrongTargetId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_like_for_Post', [
            'user' => $this->user->id,
            'dashboard' => $this->dash->id,
            'post' => Uuid::generate()->string,
        ]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testUnlikeWithWrongTargetId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->delete(
            route(
                'delete_like_for_Post',
                ['user' => $this->user->id, 'dashboard' => $this->dash->id, 'post' => Uuid::generate()->string]
            )
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUnlikeWithWrongTargetType(): void
    {
        Passport::actingAs($this->user);
        $response = $this->delete(
            route(
                'delete_like_for_Post',
                ['user' => $this->user->id, 'dashboard' => $this->dash->id, 'post' => $this->dash->id]
            )
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetLikesWithWrongTargetType(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(
            route('get_likes_from_Post', [
                'user' => $this->user->id,
                'dashboard' => $this->dash->id,
                'post' => $this->dash->id,
            ])
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testGetLikesWithWrongTargetId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(
            route(
                'get_likes_from_Post',
                ['user' => $this->user->id, 'dashboard' => $this->dash->id, 'post' => Uuid::generate()->string]
            )
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testGetLikesfromLikerWithWrongTargetId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_likes_from_liker', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
        $this->post = factory(Post::class)->create(['dashboard_id' => $this->dash->id, 'author_id' => $this->user->id]);
        $this->comm = factory(Commentary::class)->create([
            'post_id' => $this->post->id,
            'author_id' => $this->user->id,
        ]);
    }
}
