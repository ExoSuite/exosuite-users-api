<?php declare(strict_types=1);

namespace Tests\Unit;

use Tests\TestCase;
use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Models\Run;
use App\Models\Time;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Webpatser\Uuid\Uuid;

class TimeTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    public function testCreateBadTimeAtStartCheckPoint(): void
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
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
            ['current_time' => "12345678"]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateBadTimeAtDefaultAndArrivalCheckpoint(): void
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
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400"]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Time)->getFillable());
        $this->assertDatabaseHas("times", $response->decodeResponseJson());
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[1.0, 1.0], [1.0, 2.0], [2.0, 2.0], [2.0, 1.0], [1.0, 1.0]]]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
            ['current_time' => "1540382395"]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
            ['current_time' => "1540382321"]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteBadTime(): void
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
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400"]
        );
        $response = $this->delete($this->route("delete_time", [BindType::RUN => $run_id, BindType::CHECKPOINT =>
            $checkpoint_id, BindType::TIME => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetMyBadTime(): void
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
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400"]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Time)->getFillable());
        $this->assertDatabaseHas("times", $response->decodeResponseJson());
        $response = $this->get($this->route("get_my_time_by_id", [BindType::RUN => $run_id, BindType::CHECKPOINT =>
            $checkpoint_id, BindType::TIME => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetSomeoneBadTime(): void
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
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400"]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Time)->getFillable());
        $this->assertDatabaseHas("times", $response->decodeResponseJson());

        Passport::actingAs($this->user);
        $response = $this->get($this->route("get_time_by_id", [BindType::USER => $targeted_user->id, BindType::RUN
        => $run_id, BindType::CHECKPOINT => $checkpoint_id,
            BindType::TIME => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
