<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Models\CheckPoint;
use App\Models\Run;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
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
        $run = factory(Run::class)->create();
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateCheckpointWithInvalidType(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => "faulty_type",
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateTwoStartCheckPoints(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateArrivalBeforeStart(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateTwoArrivalCheckPoints(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'arrival',
        ]);
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateCheckPointTypeToStartWithAnAlreadyExistingStartCheckPoint(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        $checkpoint = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
        ]);
        $response = $this->put(
            $this->route("put_checkpoint", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint['id']]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateCheckPointTypeToArrivalWithAnAlreadyExistingArrivalCheckPoint(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        $checkpoint = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
        ]);
        factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'arrival',
        ]);
        $response = $this->put(
            $this->route("put_checkpoint", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateBadCheckPoint(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        $response = $this->put(
            $this->route("put_checkpoint", [BindType::RUN => $run['id'],
                BindType::CHECKPOINT => Uuid::generate()->string]),
            ["type" => CheckPointType::START,
                "location" => [[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetMyBadCheckpoint(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        $response = $this->get($this->route(
            "get_my_checkpoint_by_id",
            [BindType::RUN => $run['id'], BindType::CHECKPOINT => Uuid::generate()->string]
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetSomeoneBadCheckpoint(): void
    {
        $targeted_user = factory(User::class)->create();
        Passport::actingAs($targeted_user);
        $run = factory(Run::class)->create();
        $checkpoint = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        Passport::actingAs($this->user);
        $response = $this->get($this->route(
            "get_checkpoint_by_id",
            [BindType::USER => Uuid::generate()->string,
                BindType::RUN => $run['id'],
                BindType::CHECKPOINT => $checkpoint['id']]
        ));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteBadCheckPoint(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        $delete_response = $this->delete($this->route(
            "delete_checkpoint",
            [BindType::RUN => $run['id'], BindType::CHECKPOINT => Uuid::generate()->string]
        ));
        $delete_response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
