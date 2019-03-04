<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Models\CheckPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class CheckpointTest
 *
 * @package Tests\Feature
 */
class CheckpointTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    public function testCreateCheckpoint(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => str_random(30),
            "description" => str_random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 1.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", array_except($response->decodeResponseJson(), "location"));
    }

    /*

    public function testCreateAllCheckpointTypes(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => str_random(30),
            "description" => str_random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START, "location" => $this->checkpoint1Polygon]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", array_except($response->decodeResponseJson(), "location"));
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::DEFAULT, "location" => $this->checkpoint2Polygon]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", array_except($response->decodeResponseJson(), "location"));
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::ARRIVAL, "location" => $this->checkpoint3Polygon]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", array_except($response->decodeResponseJson(), "location"));
    }

    public function testShowCheckpoint(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => str_random(30),
            "description" => str_random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START, "location" => $this->checkpoint1Polygon]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->get($this->route(
            "get_checkpoint_by_id",
            [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]
        ));
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testDeleteCheckPoint(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => str_random(30),
            "description" => str_random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START, "location" => $this->checkpoint1Polygon]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->delete(
            $this->route("delete_checkpoint"),
            [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]
        );
        dd($response->decodeResponseJson());
        $this->assertDatabaseMissing("check_points", array_except($response->decodeResponseJson(), "location"));
    }
    */
}
