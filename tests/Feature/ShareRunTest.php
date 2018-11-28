<?php

namespace Tests\Feature;

use App\Models\Run;
use App\Models\Share;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;
use Laravel\Passport\Passport;

/**
 * Class ShareRunTest
 * @package Tests\Feature
 */
class ShareRunTest extends TestCase
{

    /**
     * @var Run
     */
    private $run;


    /**
     * A basic test example.
     *
     * @return User
     */
    public function testCreateShareOfARun()
    {
        $user = factory(User::class)->create();
        Passport::actingAs($user);
        $this->run = factory(Run::class)->create();

        $response = $this->json(
            Request::METHOD_POST,
            route('post_share_run'),
            ['id' => $this->run->id]
        );

        $share = new Share();
        $expectToSee = collect($share->getFillable())->diff($share->getHidden());
        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure($expectToSee->toArray());

        return $user;
    }

    /**
     * @depends testCreateShareOfARun
     * @param User $user
     */
    public function testGetAllSharedRuns(User $user)
    {
        Passport::actingAs($user);

        $response = $this->getJson(
            route('get_share_run')
        );

        $response->assertStatus(Response::HTTP_OK);
    }
}
