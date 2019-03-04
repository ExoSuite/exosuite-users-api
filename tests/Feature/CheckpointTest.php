<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Models\CheckPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Phaza\LaravelPostgis\Geometries\LineString;
use Phaza\LaravelPostgis\Geometries\Point;
use Phaza\LaravelPostgis\Geometries\Polygon;
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

    /** @var */
    private $fakeCoordinates1;

    /** @var */
    private $fakeCoordinates2;

    /** @var */
    private $fakeCoordinates3;

    /** @var */
    private $checkpoint1Polygon;

    /** @var */
    private $checkpoint2Polygon;

    /** @var */
    private $checkpoint3Polygon;

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
        dd($response->decodeResponseJson());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", array_except($response->decodeResponseJson(), "location"));
    }

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

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->fakeCoordinates1 = new LineString(
            [
                new Point(1, 1),
                new Point(1, 2),
                new Point(2, 2),
                new Point(2, 1),
                new Point(1, 1),
            ]
        );
        $this->fakeCoordinates2 = new LineString(
            [
                new Point(3, 3),
                new Point(3, 4),
                new Point(4, 4),
                new Point(4, 3),
                new Point(3, 3),
            ]
        );
        $this->fakeCoordinates3 = new LineString(
            [
                new Point(5, 5),
                new Point(5, 6),
                new Point(6, 6),
                new Point(6, 6),
                new Point(5, 5),
            ]
        );
        $this->checkpoint1Polygon = new Polygon([$this->fakeCoordinates1]);
        $this->checkpoint2Polygon = new Polygon([$this->fakeCoordinates2]);
        $this->checkpoint3Polygon = new Polygon([$this->fakeCoordinates3]);
    }
}
