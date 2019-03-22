<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Models\CheckPoint;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class CheckPointTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    public function testCreateCheckpointWithInvalidPolygon(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => Str::random(30),
            "description" => Str::random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateCheckpointWithInvalidType(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => Str::random(30),
            "description" => Str::random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => "faulty_type",
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateTwoStartCheckPoints(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => Str::random(30),
            "description" => Str::random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", Arr::except($response->decodeResponseJson(), "location"));
    }

    public function testUpdateCheckPointTypeToStartWithAnAlreadyExistingStartCheckPoint(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => Str::random(30),
            "description" => Str::random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[1.0, 1.0], [1.0, 2.0], [2.0, 2.0], [2.0, 1.0], [1.0, 1.0]]]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->put(
            $this->route("put_checkpoint", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testUpdateBadCheckPoint(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => Str::random(30),
            "description" => Str::random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response = $this->put(
            $this->route("put_checkpoint", [BindType::RUN => $run_id,
                BindType::CHECKPOINT => Uuid::generate()->string]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetMyBadCheckpoint(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => Str::random(30),
            "description" => Str::random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", Arr::except($response->decodeResponseJson(), "location"));
        $response = $this->get($this->route(
            "get_my_checkpoint_by_id",
            [BindType::RUN => $run_id, BindType::CHECKPOINT => Uuid::generate()->string]
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetSomeoneBadCheckpoint(): void
    {
        $targeted_user = factory(User::class)->create();
        Passport::actingAs($targeted_user);
        $response = $this->post($this->route('post_run'), [
            "name" => Str::random(30),
            "description" => Str::random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", Arr::except($response->decodeResponseJson(), "location"));
        $checkpoint_id = $response->decodeResponseJson('id');
        Passport::actingAs($this->user);
        $response = $this->get($this->route(
            "get_checkpoint_by_id",
            [BindType::USER => Uuid::generate()->string,
                BindType::RUN => $run_id,
                BindType::CHECKPOINT => $checkpoint_id]
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteBadCheckPoint(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            "name" => Str::random(30),
            "description" => Str::random(255),
        ]);
        $run_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", Arr::except($response->decodeResponseJson(), "location"));
        $delete_response = $this->delete($this->route(
            "delete_checkpoint",
            [BindType::RUN => $run_id, BindType::CHECKPOINT => Uuid::generate()->string]
        ));
        $delete_response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
