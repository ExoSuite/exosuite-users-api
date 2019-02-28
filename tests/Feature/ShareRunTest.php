<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\Run;
use App\Models\Share;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class ShareRunTest
 *
 * @package Tests\Feature
 */
class ShareRunTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\Run */
    private $run;


    /**
     * A basic test example.
     *
     * @return \App\Models\User
     */
    public function testCreateShareOfARun(): User
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->run = factory(Run::class)->create();

        $response = $this->json(
            Request::METHOD_POST,
            route('post_share_run'),
            ['id' => $this->run->id]
        );

        $share = new Share;
        $expectToSee = collect($share->getFillable())->diff($share->getHidden());
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure($expectToSee->toArray());

        return $user;
    }

    /**
     * @depends testCreateShareOfARun
     * @param \App\Models\User $user
     */
    public function testGetAllSharedRuns(User $user): void
    {
        Passport::actingAs($user);

        $response = $this->getJson(
            route('get_share_run')
        );

        $response->assertStatus(Response::HTTP_OK);
    }
}
