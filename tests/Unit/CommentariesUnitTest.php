<?php

namespace Tests\Unit;

use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webpatser\Uuid\Uuid;

/**
 * Class CommentariesUnitTest
 * @package Tests\Unit
 */
class CommentariesUnitTest extends TestCase
{
    /**
     * @var
     */
    private $user;

    /**
     * @var
     */
    private $user1;

    /**
     * @var
     */
    private $dash;

    /**
     * @var
     */
    private $post;

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user1 = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
        $this->post = factory(Post::class)
            ->create([
                'author_id' => $this->user->id,
                'dashboard_id' => $this->dash->id,
                'content' => str_random(10)
            ]);
    }


    /**
     * @throws \Exception
     */
    public function testCreateCommsOnFalsePostId()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route('post_commentary',
            [
                'user' => $this->user->id,
                "dashboard" => $this->dash->id,
                "post" => Uuid::generate()->string
            ]),
            ['content' => str_random(10)]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @throws \Exception
     */
    public function testCreateCommsAsUnauthorizedUser()
    {
        Passport::actingAs($this->user1);
        $response = $this->post(route('post_commentary', [
            'user' => $this->user->id,
            "dashboard" => $this->dash->id,
            "post" => $this->post->id
        ]), ['content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to post a commentary on this post"]);
    }

    /**
     * @throws \Exception
     */
    public function testGetCommsOnFalsePostId()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_commentaries_by_post_id',
            [
                'user' => $this->user,
                "dashboard" => $this->dash->id,
                "post" => Uuid::generate()->string
            ]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testGetCommsAsUnauthorizedUser()
    {
        Passport::actingAs($this->user1);
        $response = $this->get(route('get_commentaries_by_post_id',
            [
                'user' => $this->user,
                "dashboard" => $this->dash->id,
                "post" => $this->post->id
            ]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to access this post."]);
    }

    /**
     * @throws \Exception
     */
    public function testUpdateCommOnFalseCommId()
    {
        Passport::actingAs($this->user);
        $content = str_random(10);
        $response = $this->patch(route('patch_commentary', [
            'user' => $this->user->id,
            "dashboard" => $this->dash->id,
            "post" => $this->post->id,
            'commentary' => Uuid::generate()->string
        ]), [
            'content' => $content
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testUpdateCommAsUnauthorizedUser()
    {
        Passport::actingAs($this->user1);
        $content = str_random(10);
        $comm = factory(Commentary::class)->create([
            'post_id' => $this->post->id,
            'author_id' => $this->user->id,
            'content' => str_random(10)
        ]);
        $response = $this->patch(route('patch_commentary', [
            'user' => $this->user->id,
            "dashboard" => $this->dash->id,
            "post" => $this->post->id,
            'commentary' => $comm->id
        ]), [
            'content' => $content
        ]);
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allow to modify this commentary."]);

    }

    /**
     * @throws \Exception
     */
    public function testDeleteCommOnFalseCommId()
    {
        Passport::actingAs($this->user);
        $response = $this->delete(route('delete_commentary', [
            'user' => $this->user->id,
            "dashboard" => $this->dash->id,
            "post" => $this->post->id,
            'commentary_id' => Uuid::generate()->string
        ]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);

    }

    /**
     *
     */
    public function testDeleteCommAsUnauthorizedUser()
    {
        Passport::actingAs($this->user1);
        $comm = factory(Commentary::class)->create([
            'post_id' => $this->post->id,
            'author_id' => $this->user->id,
            'content' => str_random(10)
        ]);
        $response = $this->delete(route('delete_commentary', [
            'user' => $this->user->id,
            "dashboard" => $this->dash->id,
            "post" => $this->post->id,
            'commentary_id' => $comm->id
        ]));
        $response->assertStatus(Response::HTTP_FORBIDDEN);
        $response->assertJson(['message' => "Permission denied: You're not allowed to delete this post."]);

    }
}
