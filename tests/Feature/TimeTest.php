<?php declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Models\Run;
use App\Models\Time;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

class TimeTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    public function testCreateTimeAtStartCheckPoint(): void
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
    }

    public function testCreateTimeAtDefaultAndArrivalCheckpoint(): void
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
            // Timestamp value 1540382434 is equivalent to 24th November 2018, 12:00:34, so 34 seconds after
            ['current_time' => "1540382434"]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Time)->getFillable());
        $this->assertDatabaseHas("times", $response->decodeResponseJson());
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run_id]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
            // Timestamp value 1540382434 is equivalent to 24th November 2018, 12:00:55, so 21 seconds after
            ['current_time' => "1540382455"]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Time)->getFillable());
        $this->assertDatabaseHas("times", $response->decodeResponseJson());
    }

    public function testDeleteTime(): void
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
        $time = $response->decodeResponseJson();
        $time_id = $time['id'];
        $response = $this->delete($this->route("delete_time", [BindType::RUN => $run_id, BindType::CHECKPOINT =>
            $checkpoint_id, BindType::TIME => $time_id]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing("times", $time);
    }

    public function testGetMyTime(): void
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
        $time_id = $response->decodeResponseJson('id');
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Time)->getFillable());
        $this->assertDatabaseHas("times", $response->decodeResponseJson());
        $response = $this->get($this->route("get_my_time_by_id", [BindType::RUN => $run_id, BindType::CHECKPOINT =>
            $checkpoint_id, BindType::TIME => $time_id]));
        $run = $run = Run::find($run_id)->first();
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas("times", $response->decodeResponseJson());
        $this->assertForeignKeyIsExpectedID($run->creator_id, $this->user->id);
    }

    public function testGetSomeoneTime(): void
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
        $time_id = $response->decodeResponseJson('id');
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Time)->getFillable());
        $this->assertDatabaseHas("times", $response->decodeResponseJson());

        Passport::actingAs($this->user);
        $response = $this->get($this->route("get_time_by_id", [BindType::USER => $targeted_user->id, BindType::RUN
        => $run_id, BindType::CHECKPOINT => $checkpoint_id,
            BindType::TIME => $time_id]));
        $run = $run = Run::find($run_id)->first();
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas("times", $response->decodeResponseJson());
        $this->assertForeignKeyIsExpectedID($run->creator_id, $targeted_user->id);
    }

    public function testGetAllMyTimes(): void
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

        for ($i = 0; $i < 10; $i++) {
            // Creating timestamps between 1540382400 and 1540382409
            $fake_timestamp = "154038240" . $i;
            $response = $this->post(
                $this->route("post_time", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
                ['current_time' => $fake_timestamp]
            );
            $response->assertStatus(Response::HTTP_CREATED);
            $response->assertJsonStructure((new Time)->getFillable());
            $this->assertDatabaseHas("times", $response->decodeResponseJson());
        }

        $response = $this->get($this->route("get_my_times", [BindType::RUN => $run_id, BindType::CHECKPOINT =>
            $checkpoint_id]));
        $response->assertStatus(Response::HTTP_OK);

        for ($i = 0; $i < count($response->decodeResponseJson()); $i++) {
            $run = Run::find($response->decodeResponseJson()[$i]['run_id'])->first();
            $this->assertForeignKeyIsExpectedID($run->creator_id, $this->user->id);
        }
    }

    public function testGetAllSomeoneTimes(): void
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

        for ($i = 0; $i < 10; $i++) {
            // Creating timestamps between 1540382400 and 1540382409
            $fake_timestamp = "154038240" . $i;
            $response = $this->post(
                $this->route("post_time", [BindType::RUN => $run_id, BindType::CHECKPOINT => $checkpoint_id]),
                ['current_time' => $fake_timestamp]
            );
            $response->assertStatus(Response::HTTP_CREATED);
            $response->assertJsonStructure((new Time)->getFillable());
            $this->assertDatabaseHas("times", $response->decodeResponseJson());
        }

        Passport::actingAs($this->user);
        $response = $this->get($this->route("get_times", [BindType::USER => $targeted_user->id, BindType::RUN =>
            $run_id, BindType::CHECKPOINT => $checkpoint_id]));
        $response->assertStatus(Response::HTTP_OK);

        for ($i = 0; $i < count($response->decodeResponseJson()); $i++) {
            $run = Run::find($response->decodeResponseJson()[$i]['run_id'])->first();
            $this->assertForeignKeyIsExpectedID($run->creator_id, $targeted_user->id);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
