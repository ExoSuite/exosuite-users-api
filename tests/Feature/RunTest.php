<?php

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\Visibility;
use App\Models\Run;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class RunTest
 * @package Tests\Feature
 */
class RunTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var User
     */
    private $user;
    /**
     * @var Run
     */
    private $run;

    /**
     *
     */
    public function testCreateRun()
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_run'),
            [
                "name" => str_random(30)
            ]
        );
        $run = new Run();
        $expect = collect($run->getFillable())->diff(["description"]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($expect->toArray());
    }

    /**
     *
     */
    public function testCreateRunWithDescription()
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => str_random(30),
            "description" => str_random(255)
        ]);
        $run = new Run();
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($run->getFillable());
    }

    /**
     *
     */
    public function testCreateRunFullFilled()
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => str_random(30),
            "description" => str_random(255),
            "visibility" => Visibility::PUBLIC
        ]);
        $run = new Run();
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($run->getFillable());
        $response->assertJson(["visibility" => Visibility::PUBLIC]);
    }

    /**
     *
     */
    public function testUpdateRun()
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route("patch_run", [BindType::RUN => $this->run->id]), [
            "name" => str_random(30),
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     *
     */
    public function testUpdateRunDescription()
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route("patch_run", [BindType::RUN => $this->run->id]), [
            "description" => str_random(255),
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     *
     */
    public function testUpdateRunVisibility()
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route("patch_run", [BindType::RUN => $this->run->id]), [
            "visibility" => Visibility::PRIVATE,
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     *
     */
    public function testUpdateRunFullFilled()
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route("patch_run", [BindType::RUN => $this->run->id]), [
            "visibility" => Visibility::PRIVATE,
            "name" => str_random(30),
            "description" => str_random(255)
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     *
     */
    public function testGetAllRuns()
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();

        $response = $this->getJson(
            route('get_run')
        );

        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     *
     */
    public function testGetRunById()
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->get($this->route("get_run_by_id", [BindType::RUN => $this->run->id]));
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
