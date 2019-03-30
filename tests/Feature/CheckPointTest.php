<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Models\CheckPoint;
use App\Models\Run;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class CheckPointTest
 *
 * @package Tests\Feature
 */
class CheckPointTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    public function testCreateCheckpoint(): void
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
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateAllCheckpointTypes(): void
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
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[5.0, 5.0], [5.0, 6.0], [6.0, 6.0], [6.0, 5.0], [5.0, 5.0]]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", Arr::except($response->decodeResponseJson(), "location"));
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[10.0, 10.0], [10.0, 11.0], [11.0, 11.0], [11.0, 10.0], [10.0, 10.0]]]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new CheckPoint)->getFillable());
        $this->assertDatabaseHas("check_points", Arr::except($response->decodeResponseJson(), "location"));
    }

    public function testGetMyCheckpoint(): void
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
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->get($this->route(
            "get_my_checkpoint_by_id",
            [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]
        ));
        $run = Run::find($run_id)->first();
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas("check_points", Arr::except($response->decodeResponseJson(), "location"));
        $this->assertForeignKeyIsExpectedID($run->creator_id, $this->user->id);
    }

    public function testGetSomeoneCheckpoint(): void
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
            [BindType::USER => $targeted_user->id, BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]
        ));
        $run = Run::find($run_id)->first();
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas("check_points", Arr::except($response->decodeResponseJson(), "location"));
        $this->assertForeignKeyIsExpectedID($run->creator_id, $targeted_user->id);
    }

    public function testGetAllMyCheckpoints(): void
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

        for ($i = 0; $i < 10; $i++) {
            $response = $this->post(
                $this->route("post_checkpoint", [BindType::RUN => $run_id]),
                ["type" => CheckPointType::DEFAULT,
                    "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
            );
            $response->assertStatus(Response::HTTP_CREATED);
            $response->assertJsonStructure((new CheckPoint)->getFillable());
            $this->assertDatabaseHas("check_points", Arr::except($response->decodeResponseJson(), "location"));
        }

        $response = $this->get($this->route(
            "get_my_checkpoints",
            [BindType::RUN => $run_id]
        ));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(11, count(Arr::except($response->decodeResponseJson(), 'location')));

        for ($i = 0; $i < count($response->decodeResponseJson()); $i++) {
            $run = Run::find($response->decodeResponseJson()[$i]['run_id'])->first();
            $this->assertForeignKeyIsExpectedID($run->creator_id, $this->user->id);
        }
    }

    public function testGetAllSomeoneCheckpoints(): void
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

        for ($i = 0; $i < 10; $i++) {
            $response = $this->post(
                $this->route("post_checkpoint", [BindType::RUN => $run_id]),
                ["type" => CheckPointType::DEFAULT,
                    "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
            );
            $response->assertStatus(Response::HTTP_CREATED);
            $response->assertJsonStructure((new CheckPoint)->getFillable());
            $this->assertDatabaseHas("check_points", Arr::except($response->decodeResponseJson(), "location"));
        }

        Passport::actingAs($this->user);
        $response = $this->get($this->route(
            "get_checkpoints",
            [BindType::USER => $targeted_user->id, BindType::RUN => $run_id]
        ));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(11, count(Arr::except($response->decodeResponseJson(), 'location')));

        for ($i = 0; $i < count($response->decodeResponseJson()); $i++) {
            $run = Run::find($response->decodeResponseJson()[$i]['run_id'])->first();
            $this->assertForeignKeyIsExpectedID($run->creator_id, $targeted_user->id);
        }
    }

    public function testDeleteCheckPoint(): void
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
        $checkpoint_id = $response->decodeResponseJson('id');
        $delete_response = $this->delete($this->route(
            "delete_checkpoint",
            [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]
        ));
        $this->assertDatabaseMissing("check_points", Arr::except($response->decodeResponseJson(), "location"));
        $delete_response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUpdateCheckPoint(): void
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
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->put(
            $this->route("put_checkpoint", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
