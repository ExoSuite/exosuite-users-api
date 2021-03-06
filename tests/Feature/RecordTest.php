<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\CheckPointType;
use App\Http\Controllers\CheckPoint\CheckPointController;
use App\Models\CheckPoint;
use App\Models\Record;
use App\Models\Run;
use App\Models\User;
use App\Passport\Passport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
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
        factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
        ]);
        $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[1.0, 1.0], [1.0, 2.0], [2.0, 2.0], [2.0, 1.0], [1.0, 1.0]]]
        );
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
        $keys_to_rm = ["best_segments", "distance_between_cps", "best_speed_between_cps"];
        $final_array = Arr::except($response->decodeResponseJson(), $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array);
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
        $this->post(
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
        $this->post(
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
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint_id]),
            // Timestamp value 1540382434 is equivalent to 24th November 2018, 12:00:55, so 21 seconds after
            ['current_time' => "1540382455", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $response = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Record)->getFillable());
        $keys_to_rm = ["best_segments", "distance_between_cps", "best_speed_between_cps"];
        $final_array = Arr::except($response->decodeResponseJson(), $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array);
        $record_id = $response->decodeResponseJson('id');
        $response = $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new Record)->getFillable());
        $keys_to_rm = ["best_segments", "distance_between_cps", "best_speed_between_cps"];
        $final_array = Arr::except($response->decodeResponseJson(), $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array);
    }

    public function testGetMyRecords(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $cp1 = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
            "location" => CheckPointController::createPolygonFromArray([[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]),
        ]);
        $cp2 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[1.0, 1.0], [1.0, 2.0], [2.0, 2.0], [2.0, 1.0], [1.0, 1.0]]]
        );
        $checkpoint2_id = $cp2->decodeResponseJson('id');
        $cp3 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint3_id = $cp3->decodeResponseJson('id');
        $cp4 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[3.0, 3.0], [3.0, 4.0], [4.0, 4.0], [4.0, 3.0], [3.0, 3.0]]]
        );
        $checkpoint4_id = $cp4->decodeResponseJson('id');
        $cp5 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[4.0, 4.0], [4.0, 5.0], [5.0, 5.0], [5.0, 4.0], [4.0, 4.0]]]
        );
        $checkpoint5_id = $cp5->decodeResponseJson('id');

        // 1st UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382415", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382430", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382445", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382460", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $record = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $record_id = $record->decodeResponseJson("id");
        $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );

        // 2nd UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382500", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382509", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382514", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382526", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382562", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $response = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $record2_id = $response->decodeResponseJson("id");
        $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record2_id]),
            ["user_run_id" => $user_run_id]
        );
        $response = $this->get(
            $this->route("get_my_records", [BindType::RUN => $run['id']])
        );
        $response->assertStatus(Response::HTTP_OK);
        $keys_to_rm = ["best_segments", "distance_between_cps", "best_speed_between_cps"];
        $final_array1 = Arr::except($response->decodeResponseJson()['data'][0], $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array1);
        $final_array2 = Arr::except($response->decodeResponseJson()['data'][1], $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array2);
    }

    public function testGetMyRecordById(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $cp1 = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
            "location" => CheckPointController::createPolygonFromArray([[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]),
        ]);
        $cp2 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[1.0, 1.0], [1.0, 2.0], [2.0, 2.0], [2.0, 1.0], [1.0, 1.0]]]
        );
        $checkpoint2_id = $cp2->decodeResponseJson('id');
        $cp3 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint3_id = $cp3->decodeResponseJson('id');
        $cp4 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[3.0, 3.0], [3.0, 4.0], [4.0, 4.0], [4.0, 3.0], [3.0, 3.0]]]
        );
        $checkpoint4_id = $cp4->decodeResponseJson('id');
        $cp5 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[4.0, 4.0], [4.0, 5.0], [5.0, 5.0], [5.0, 4.0], [4.0, 4.0]]]
        );
        $checkpoint5_id = $cp5->decodeResponseJson('id');

        // 1st UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382415", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382430", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382445", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382460", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $record = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $record_id = $record->decodeResponseJson("id");
        $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );
        $record = $this->get(
            $this->route("get_my_record_by_id", [BindType::RUN => $run['id'], BindType::RECORD => $record_id])
        );
        $record->assertStatus(Response::HTTP_OK);
        $record->assertJsonStructure((new Record)->getFillable());
        $keys_to_rm = ["best_segments", "distance_between_cps", "best_speed_between_cps"];
        $final_array1 = Arr::except($record->decodeResponseJson(), $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array1);
        $final_array2 = Arr::except($record->decodeResponseJson(), $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array2);
    }

    public function testGetSomeoneRecords(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $cp1 = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
            "location" => CheckPointController::createPolygonFromArray([[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]),
        ]);
        $cp2 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[1.0, 1.0], [1.0, 2.0], [2.0, 2.0], [2.0, 1.0], [1.0, 1.0]]]
        );
        $checkpoint2_id = $cp2->decodeResponseJson('id');
        $cp3 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint3_id = $cp3->decodeResponseJson('id');
        $cp4 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[3.0, 3.0], [3.0, 4.0], [4.0, 4.0], [4.0, 3.0], [3.0, 3.0]]]
        );
        $checkpoint4_id = $cp4->decodeResponseJson('id');
        $cp5 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[4.0, 4.0], [4.0, 5.0], [5.0, 5.0], [5.0, 4.0], [4.0, 4.0]]]
        );
        $checkpoint5_id = $cp5->decodeResponseJson('id');

        // 1st UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382415", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382430", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382445", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382460", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $record = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $record_id = $record->decodeResponseJson("id");
        $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );

        // 2nd UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382500", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382509", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382514", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382526", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382562", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $response = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $record2_id = $response->decodeResponseJson("id");
        $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record2_id]),
            ["user_run_id" => $user_run_id]
        );
        Passport::actingAs($this->user2);
        $response = $this->get(
            $this->route("get_user_records", [BindType::USER => $this->user->id, BindType::RUN => $run['id']])
        );
        $response->assertStatus(Response::HTTP_OK);
        $keys_to_rm = ["best_segments", "distance_between_cps", "best_speed_between_cps"];
        $final_array1 = Arr::except($response->decodeResponseJson()['data'][0], $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array1);
        $final_array2 = Arr::except($response->decodeResponseJson()['data'][1], $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array2);
    }

    public function testGetSomeoneRecordById(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $cp1 = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
            "location" => CheckPointController::createPolygonFromArray([[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]),
        ]);
        $cp2 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[1.0, 1.0], [1.0, 2.0], [2.0, 2.0], [2.0, 1.0], [1.0, 1.0]]]
        );
        $checkpoint2_id = $cp2->decodeResponseJson('id');
        $cp3 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint3_id = $cp3->decodeResponseJson('id');
        $cp4 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[3.0, 3.0], [3.0, 4.0], [4.0, 4.0], [4.0, 3.0], [3.0, 3.0]]]
        );
        $checkpoint4_id = $cp4->decodeResponseJson('id');
        $cp5 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[4.0, 4.0], [4.0, 5.0], [5.0, 5.0], [5.0, 4.0], [4.0, 4.0]]]
        );
        $checkpoint5_id = $cp5->decodeResponseJson('id');

        // 1st UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382415", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382430", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382445", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382460", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $record = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $record_id = $record->decodeResponseJson("id");
        $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );
        Passport::actingAs($this->user2);
        $record = $this->get(
            $this->route("get_user_record_by_id", [BindType::USER => $this->user2, BindType::RUN => $run['id'],
                BindType::RECORD
                => $record_id])
        );
        $record->assertStatus(Response::HTTP_OK);
        $record->assertJsonStructure((new Record)->getFillable());
        $keys_to_rm = ["best_segments", "distance_between_cps", "best_speed_between_cps"];
        $final_array1 = Arr::except($record->decodeResponseJson(), $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array1);
        $final_array2 = Arr::except($record->decodeResponseJson(), $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array2);
    }

    public function testBestOfSumCalculation(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $cp1 = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
            "location" => CheckPointController::createPolygonFromArray([[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]),
        ]);
        $cp2 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[1.0, 1.0], [1.0, 2.0], [2.0, 2.0], [2.0, 1.0], [1.0, 1.0]]]
        );
        $checkpoint2_id = $cp2->decodeResponseJson('id');
        $cp3 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint3_id = $cp3->decodeResponseJson('id');
        $cp4 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[3.0, 3.0], [3.0, 4.0], [4.0, 4.0], [4.0, 3.0], [3.0, 3.0]]]
        );
        $checkpoint4_id = $cp4->decodeResponseJson('id');
        $cp5 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[4.0, 4.0], [4.0, 5.0], [5.0, 5.0], [5.0, 4.0], [4.0, 4.0]]]
        );
        $checkpoint5_id = $cp5->decodeResponseJson('id');

        // 1st UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382415", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382430", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382445", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382460", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $response = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $record_id = $response->decodeResponseJson("id");
        $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );

        // 2nd UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382500", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382509", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382514", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382526", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382562", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $record = $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );

        $response = $this->get(
            $this->route("get_my_records", [BindType::RUN => $run['id']])
        );
        $response->assertStatus(Response::HTTP_OK);
        $keys_to_rm = ["best_segments", "distance_between_cps", "best_speed_between_cps"];
        $final_array = Arr::except($record->decodeResponseJson(), $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array);
    }

    public function testAllFieldsCalculations(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $cp1 = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
            "location" => CheckPointController::createPolygonFromArray([[0.0, 0.0],
                [0.0, 0.001],
                [0.001, 0.001],
                [0.001, 0.0],
                [0.0, 0.0]]),
        ]);
        $cp2 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[0.001, 0.001], [0.001, 0.002], [0.002, 0.002], [0.002, 0.001], [0.001, 0.001]]]
        );
        $checkpoint2_id = $cp2->decodeResponseJson('id');
        $cp3 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[0.002, 0.002], [0.002, 0.003], [0.003, 0.003], [0.003, 0.002], [0.002, 0.002]]]
        );
        $checkpoint3_id = $cp3->decodeResponseJson('id');
        $cp4 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[0.003, 0.003], [0.003, 0.004], [0.004, 0.004], [0.004, 0.003], [0.003, 0.003]]]
        );
        $checkpoint4_id = $cp4->decodeResponseJson('id');
        $cp5 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[0.004, 0.004], [0.004, 0.005], [0.005, 0.005], [0.005, 0.004], [0.004, 0.004]]]
        );
        $checkpoint5_id = $cp5->decodeResponseJson('id');

        // 1st UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382436", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382488", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382519", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382561", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $response = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $record_id = $response->decodeResponseJson("id");
        $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );

        // 2nd UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382600", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382631", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382675", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382702", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382743", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $record2 = $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );

        $this->get(
            $this->route("get_my_records", [BindType::RUN => $run['id']])
        );

        $record2->assertStatus(Response::HTTP_OK);
        $record2->assertJsonStructure((new Record)->getFillable());
        $keys_to_rm = ["best_segments", "distance_between_cps", "best_speed_between_cps"];
        $final_array = Arr::except($record2->decodeResponseJson(), $keys_to_rm);
        $this->assertDatabaseHas("records", $final_array);
        $this->assertEquals($record2->decodeResponseJson('best_time_user_run_id'), $user_run_id);
    }

    public function testDeleteRecord(): void
    {
        Passport::actingAs($this->user);
        $run = factory(Run::class)->create();
        $cp1 = factory(CheckPoint::class)->create([
            'run_id' => $run['id'],
            'type' => 'start',
            "location" => CheckPointController::createPolygonFromArray([[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0], [0.0, 0.0]]),
        ]);
        $cp2 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[1.0, 1.0], [1.0, 2.0], [2.0, 2.0], [2.0, 1.0], [1.0, 1.0]]]
        );
        $checkpoint2_id = $cp2->decodeResponseJson('id');
        $cp3 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[2.0, 2.0], [2.0, 3.0], [3.0, 3.0], [3.0, 2.0], [2.0, 2.0]]]
        );
        $checkpoint3_id = $cp3->decodeResponseJson('id');
        $cp4 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::DEFAULT,
                "location" => [[3.0, 3.0], [3.0, 4.0], [4.0, 4.0], [4.0, 3.0], [3.0, 3.0]]]
        );
        $checkpoint4_id = $cp4->decodeResponseJson('id');
        $cp5 = $this->post(
            $this->route("post_checkpoint", [BindType::RUN => $run['id']]),
            ["type" => CheckPointType::ARRIVAL,
                "location" => [[4.0, 4.0], [4.0, 5.0], [5.0, 5.0], [5.0, 4.0], [4.0, 4.0]]]
        );
        $checkpoint5_id = $cp5->decodeResponseJson('id');

        // 1st UserRun
        $user_run = $this->post(
            $this->route("post_user_run", [BindType::RUN => $run['id']])
        );
        $user_run_id = $user_run->decodeResponseJson('id');
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382415", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382421", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382434", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382456", "user_run_id" => $user_run_id]
        );
        $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $response = $this->post(
            $this->route("post_record", [BindType::RUN => $run['id']]),
            ["user_run_id" => $user_run_id]
        );
        $record_id = $response->decodeResponseJson("id");
        $response = $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );
        $delete_request = $this->delete(
            $this->route("delete_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id])
        );
        $delete_request->assertStatus(Response::HTTP_NO_CONTENT);
        $keys_to_rm = ["best_segments", "distance_between_cps", "best_speed_between_cps"];
        $final_array = Arr::except($response->decodeResponseJson(), $keys_to_rm);
        $this->assertDatabaseMissing("records", $final_array);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }
}
