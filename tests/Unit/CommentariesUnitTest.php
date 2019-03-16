<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Models\Commentary;
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
 * Class CommentariesUnitTest
 *
 * @package Tests\Unit
 */
class CommentariesUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\User */
    private $user1;

    /** @var \App\Models\Dashboard */
    private $dash;

    /** @var \App\Models\Post */
    private $post;

    /**
     * @throws \Exception
     */
    public function testCreateCommsOnFalsePostId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            route(
                'post_commentary',
                [
                    'user' => $this->user->id,
                    'dashboard' => $this->dash->id,
                    'post' => Uuid::generate()->string,
                ]
            ),
            ['content' => Str::random(10)]
        );

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testCreateCommsAsUnauthorizedUser(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->post(route('post_commentary', [
            'user' => $this->user->id,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id,
        ]), ['content' => Str::random(10)]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws \Exception
     */
    public function testGetCommsOnFalsePostId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route(
            'get_commentaries_by_post_id',
            [
                'user' => $this->user,
                'dashboard' => $this->dash->id,
                'post' => Uuid::generate()->string,
            ]
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetCommsAsUnauthorizedUser(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->get(route(
            'get_commentaries_by_post_id',
            [
                'user' => $this->user,
                'dashboard' => $this->dash->id,
                'post' => $this->post->id,
            ]
        ));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateCommOnFalseCommId(): void
    {
        Passport::actingAs($this->user);
        $content = Str::random(10);
        $response = $this->patch(route('patch_commentary', [
            'user' => $this->user->id,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id,
            'commentary' => Uuid::generate()->string,
        ]), [
            'content' => $content,
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateCommAsUnauthorizedUser(): void
    {
        Passport::actingAs($this->user1);
        $content = Str::random(10);
        $comm = factory(Commentary::class)->create([
            'post_id' => $this->post->id,
            'author_id' => $this->user->id,
            'content' => Str::random(10),
        ]);
        $response = $this->patch(route('patch_commentary', [
            'user' => $this->user->id,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id,
            'commentary' => $comm->id,
        ]), [
            'content' => $content,
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    /**
     * @throws \Exception
     */
    public function testDeleteCommOnFalseCommId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_commentary', [
            'user' => $this->user->id,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id,
            'commentary_id' => Uuid::generate()->string,
        ]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteCommAsUnauthorizedUser(): void
    {
        Passport::actingAs($this->user1);
        $comm = factory(Commentary::class)->create([
            'post_id' => $this->post->id,
            'author_id' => $this->user->id,
            'content' => Str::random(10),
        ]);
        $response = $this->delete(route('delete_commentary', [
            'user' => $this->user->id,
            'dashboard' => $this->dash->id,
            'post' => $this->post->id,
            'commentary_id' => $comm->id,
        ]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
        $this->post = factory(Post::class)
            ->create([
                'author_id' => $this->user->id,
                'dashboard_id' => $this->dash->id,
                'content' => Str::random(10),
            ]);
    }
}
