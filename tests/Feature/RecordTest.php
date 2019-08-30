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
        $response = $this->post(
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
        $record_id = $response->decodeResponseJson("id");
        $response = $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );
        $response = $this->get(
            $this->route("get_my_records", [BindType::RUN => $run['id']])
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new Record)->getFillable());
        $this->assertDatabaseHas("records", $response->decodeResponseJson());
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
        $time1 = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
        );
        $time2 = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382415", "user_run_id" => $user_run_id]
        );
        $time3 = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382430", "user_run_id" => $user_run_id]
        );
        $time4 = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382445", "user_run_id" => $user_run_id]
        );
        $time5 = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382460", "user_run_id" => $user_run_id]
        );
        $response = $this->patch(
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
        $response = $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $response = $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );

        $response = $this->get(
            $this->route("get_my_records", [BindType::RUN => $run['id']])
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new Record)->getFillable());
        $this->assertDatabaseHas("records", $response->decodeResponseJson());
    }

    public function testAllFieldsCalculations(): void
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
        $time1 = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $cp1['id']]),
            // Timestamp value 1540382400 is equivalent to 24th November 2018, 12:00:00
            ['current_time' => "1540382400", "user_run_id" => $user_run_id]
        );
        $time2 = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint2_id]),
            ['current_time' => "1540382415", "user_run_id" => $user_run_id]
        );
        $time3 = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382421", "user_run_id" => $user_run_id]
        );
        $time4 = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382434", "user_run_id" => $user_run_id]
        );
        $time5 = $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382456", "user_run_id" => $user_run_id]
        );
        $response = $this->patch(
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
            ['current_time' => "1540382512", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint3_id]),
            ['current_time' => "1540382519", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint4_id]),
            ['current_time' => "1540382530", "user_run_id" => $user_run_id]
        );
        $this->post(
            $this->route("post_time", [BindType::RUN => $run['id'], BindType::CHECKPOINT => $checkpoint5_id]),
            ['current_time' => "1540382549", "user_run_id" => $user_run_id]
        );
        $response = $this->patch(
            $this->route("patch_user_run", [BindType::RUN => $run['id'], BindType::USER_RUN =>
                $user_run_id])
        );
        $response = $this->patch(
            $this->route("patch_record", [BindType::RUN => $run['id'], BindType::RECORD => $record_id]),
            ["user_run_id" => $user_run_id]
        );

        $response = $this->get(
            $this->route("get_my_records", [BindType::RUN => $run['id']])
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure((new Record)->getFillable());
        $this->assertDatabaseHas("records", $response->decodeResponseJson());
        $this->assertEquals($response->decodeResponseJson('best_time_user_run_id'), $user_run_id);
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
        $response = $this->patch(
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
        $this->assertDatabaseMissing("records", $response->decodeResponseJson());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
