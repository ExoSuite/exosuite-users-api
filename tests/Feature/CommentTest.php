<?php

namespace Tests\Feature;

use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class CommentTest
 * @package Tests\Feature
 */
class CommentTest extends TestCase
{
    /**
     * @var
     */
    private $user;

    /**
     * @var
     */
    private $dash;

    /**
     * @var
     */
    private $post;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreate()
    {
        Passport::actingAs($this->user);
        $response = $this->post(route(
            'post_commentary',
            [
                'user' => $this->user->id,
                'dashboard' => $this->dash->id,
                "post" => $this->post->id
            ]
        ), ['content' => str_random(10)]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Commentary())->getFillable());
        $this->assertDatabaseHas('commentaries', $response->decodeResponseJson());
    }

    /**
     *
     */
    public function testGetComms()
    {
        Passport::actingAs($this->user);
        for ($it = 0; $it < 5; $it++) {
            factory(Commentary::class)->create(['post_id' => $this->post->id, 'author_id' => $this->user->id, 'content' => str_random(10)]);
        }
        $response = $this->get(route('get_commentaries_by_post_id', [
            'user' => $this->user->id,
            'dashboard' => $this->dash->id,
            "post" => $this->post->id
        ]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(5, count($response->decodeResponseJson()));
    }

    /**
     *
     */
    public function testUpdateComm()
    {
        Passport::actingAs($this->user);
        $content = str_random(10);
        $comm = factory(Commentary::class)->create(
            [
                'post_id' => $this->post->id,
                'author_id' => $this->user->id,
                'content' => str_random(10)
            ]
        );

        $response = $this->patch(
            route('patch_commentary', [
                'user' => $this->user->id,
                'dashboard' => $this->dash->id,
                "post" => $this->post->id,
                'commentary' => $comm->id
            ]),
            ['content' => $content]
        );
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas(
            'commentaries',
            [
                'id' => $comm->id,
                'author_id' => $this->user->id,
                'content' => $content
            ]
        );
    }

    /**
     *
     */
    public function testDeleteComm()
    {
        Passport::actingAs($this->user);
        $post_resp = $this->post(
            route('post_commentary', [
                'user' => $this->user->id,
                'dashboard' => $this->dash->id,
                "post" => $this->post->id
            ]),
            ['content' => str_random(10)]
        );
        $response = $this->delete(
            route(
                'delete_commentary',
                [
                    'user' => $this->user->id,
                    'dashboard' => $this->dash->id,
                    "post" => $this->post->id,
                    'commentary' => $post_resp->decodeResponseJson('id')
                ]
            )
        );
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing('commentaries', $post_resp->decodeResponseJson());
    }

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
        $this->post = factory(Post::class)
            ->create(['author_id' => $this->user->id, 'dashboard_id' => $this->dash->id, 'content' => str_random(10)]);
    }
}
