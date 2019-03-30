<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Models\CheckPoint;
use App\Models\Run;
use App\Models\Time;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class TimeTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    public function testCreateBadTimeAtStartCheckPoint(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $checkpoint = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint['id']]),
            ['current_time' => "12345678"]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateBadTimeAtDefaultAndArrivalCheckpoint(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint_id]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400"]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Time)->getFillable());
        $this->assertDatabaseHas("times", $response->decodeResponseJson());
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[1.0, 1.0], [1.0, 2.0], [2.0, 2.0], [2.0, 1.0], [1.0, 1.0]]]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint_id]),
            ['current_time' => "1540382395"]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint_id]),
            ['current_time' => "1540382321"]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteBadTime(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $checkpoint = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        factory(Time::class)->create([
            'check_point_id' => $checkpoint['id'],
            'run_id' => $run['id'],
        ]);
        $response = $this->delete($this->route("delete_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT =>
            $checkpoint['id'], BindType::TIME => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetMyBadTime(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $checkpoint = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        factory(Time::class)->create([
            'check_point_id' => $checkpoint['id'],
            'run_id' => $run['id'],
        ]);
        $response = $this->get($this->route("get_my_time_by_id", [BindType::RUN => $run['id'], BindType::CHECKPOINT =>
            $checkpoint['id'], BindType::TIME => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetSomeoneBadTime(): void
    {
        $targeted_user = factory(User::class)->create();
        Passport::actingAs($targeted_user);
        $run = factory(Run::class)->create();
        $checkpoint = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        factory(Time::class)->create([
            'check_point_id' => $checkpoint['id'],
            'run_id' => $run['id'],
        ]);

        Passport::actingAs($this->user);
        $response = $this->get($this->route("get_time_by_id", [BindType::USER => $targeted_user->id, BindType::RUN
        => $run['id'], BindType::CHECKPOINT => $checkpoint['id'],
            BindType::TIME => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
