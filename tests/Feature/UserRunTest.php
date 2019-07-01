<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Models\CheckPoint;
use App\Models\Run;
use App\Models\Time;
use App\Models\User;
use App\Models\UserRun;
use App\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Tests\TestCase;

class UserRunTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    public function testCreateUserRun(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $response = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new UserRun)->getFillable());
        $this->assertDatabaseHas("user_runs", $response->decodeResponseJson());
    }

    public function testUpdateUserRun(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $checkpoint = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
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
            // Timestamp value 1540382434 is equivalent to 24th November 2018, 12:00:34, so 34 seconds after
            ['current_time' => "1540382434", "user_run_id" => $user_run_id]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Time)->getFillable());
        $this->assertDatabaseHas("times", $response->decodeResponseJson());
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint_id]),
            // Timestamp value 1540382434 is equivalent to 24th November 2018, 12:00:55, so 21 seconds after
            ['current_time' => "1540382455", "user_run_id" => $user_run_id]
        );
        $response = $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new UserRun)->getFillable());
        $this->assertDatabaseHas("user_runs", $response->decodeResponseJson());
    }

    public function testGetMyUserRunByID(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $checkpoint = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
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
            // Timestamp value 1540382434 is equivalent to 24th November 2018, 12:00:34, so 34 seconds after
            ['current_time' => "1540382434", "user_run_id" => $user_run_id]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Time)->getFillable());
        $this->assertDatabaseHas("times", $response->decodeResponseJson());
        $response = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint_id = $response->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint_id]),
            // Timestamp value 1540382434 is equivalent to 24th November 2018, 12:00:55, so 21 seconds after
            ['current_time' => "1540382455", "user_run_id" => $user_run_id]
        );
        $response = $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $response = $this->get(
            $this->route("get_my_user_run_by_id", [BindType::RUN => $run['id'], BindType::USER_RUN =>
            $user_run_id])
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new UserRun)->getFillable());
        $this->assertDatabaseHas("user_runs", Arr::except($response->decodeResponseJson(), "times"));
    }

    public function testGetAllMyUserRuns(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();

        for ($i = 0; $i < 10; $i++) {
            $this->post(
                $this->route("post_user_run", [BindType::RUN => $run['id']])
            );
        }

        $response = $this->get(
            $this->route('get_my_user_runs', [BindType::RUN => $run['id']])
        );
        $response->assertStatus(Response::HTTP_OK);

        for ($i = 0; $i < count($response->decodeResponseJson('data')); $i++) {
            $run = Run::find($response->decodeResponseJson('data')[$i]['run_id'])->first();
            $this->assertForeignKeyIsExpectedID($run->creator_id, $this->user->id);
            $expected_user_id = $response->decodeResponseJson('data')[$i]['user_id'];
            $this->assertForeignKeyIsExpectedID($expected_user_id, $this->user->id);
        }
    }

    public function testGetSomeoneUserRunByID(): void
    {
        $targeted_user = factory(User::class)->create();
        Passport::actingAs($targeted_user);
        $run = factory(Run::class)->create();
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $checkpoint = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        factory(Time::class)->create([
            'check_point_id' => $checkpoint['id'],
            'run_id' => $run['id'],
            'user_run_id' => $user_run->decodeResponseJson('id'),
        ]);
        Passport::actingAs($this->user);
        $response = $this->get($this->route("get_user_run_by_id", [BindType::USER => $targeted_user->id, BindType::RUN
        => $run['id'], BindType::USER_RUN => $user_run_id]));
        $run = $run = Run::find($run['id'])->first();
        $response->assertStatus(Response::HTTP_OK);
        $this->assertDatabaseHas("user_runs", Arr::except($response->decodeResponseJson(), "times"));
        $this->assertForeignKeyIsExpectedID($run->creator_id, $targeted_user->id);
    }

    public function testGetSomeoneAllUserRuns(): void
    {
        $targeted_user = factory(User::class)->create();
        Passport::actingAs($targeted_user);
        $run = factory(Run::class)->create();

        for ($i = 0; $i < 10; $i++) {
            $this->post(
                $this->route("post_user_run", [BindType::RUN => $run['id']])
            );
        }

        Passport::actingAs($this->user);
        $response = $this->get(
            $this->route('get_user_runs', [BindType::USER => $targeted_user['id'],
                BindType::RUN => $run['id']])
        );
        $response->assertStatus(Response::HTTP_OK);

        for ($i = 0; $i < count($response->decodeResponseJson('data')); $i++) {
            $run = Run::find($response->decodeResponseJson('data')[$i]['run_id'])->first();
            $this->assertForeignKeyIsExpectedID($run->creator_id, $targeted_user->id);
            $expected_user_id = $response->decodeResponseJson('data')[$i]['user_id'];
            $this->assertForeignKeyIsExpectedID($expected_user_id, $targeted_user->id);
        }
    }

    public function testDeleteUserRun(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $response = $this->delete(
            $this->route("delete_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run->decodeResponseJson('id')])
        );
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing("user_runs", $user_run->decodeResponseJson());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
