<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Models\CheckPoint;
use App\Models\Record;
use App\Models\Run;
use App\Models\User;
use App\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Tests\TestCase;

class RecordTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    public function testCreateDefaultRecord(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $response = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Record)->getFillable());
        $this->assertDatabaseHas("records", $response->decodeResponseJson());
    }

    public function testCreateAndUpdateRecord(): void
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
        $response = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Record)->getFillable());
        $this->assertDatabaseHas("records", $response->decodeResponseJson());
        $record_id = $response->decodeResponseJson('id');
        $response = $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new Record)->getFillable());
        $this->assertDatabaseHas("records", $response->decodeResponseJson());
    }

    public function testGetMyRecord(): void
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
        $response = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $response = $this->get(
            $this->route("get_my_records", [BindType::RUN => $run['id']])
        );
        dd($response->decodeResponseJson());
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new Record)->getFillable());
        $this->assertDatabaseHas("records", $response->decodeResponseJson());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
