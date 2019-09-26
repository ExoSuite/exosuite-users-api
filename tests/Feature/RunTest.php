<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\Visibility;
use App\Http\Controllers\Run\RunController;
use App\Models\CheckPoint;
use App\Models\Run;
use App\Models\Time;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class RunTest
 *
 * @package Tests\Feature
 */
class RunTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\Run */
    private $run;

    public function testCreateRun(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_run'),
            ['name' => Str::random(30)]
        );
        $run = new Run;
        $expect = collect($run->getFillable())->diff(['description']);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($expect->toArray());
    }

    public function testCreateRunWithDescription(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            'name' => Str::random(30),
            'description' => Str::random(255),
        ]);
        $run = new Run;
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($run->getFillable());
    }

    public function testCreateRunFullFilled(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            'name' => Str::random(30),
            'description' => Str::random(255),
            'visibility' => Visibility::PUBLIC,
        ]);
        $run = new Run;
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure($run->getFillable());
        $response->assertJson(['visibility' => Visibility::PUBLIC]);
    }

    public function testUpdateRun(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route('patch_run', [BindType::RUN => $this->run->id]), [
            'name' => Str::random(30),
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateRunDescription(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route('patch_run', [BindType::RUN => $this->run->id]), [
            'description' => Str::random(255),
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateRunVisibility(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route('patch_run', [BindType::RUN => $this->run->id]), [
            'visibility' => Visibility::PRIVATE,
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testUpdateRunFullFilled(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route('patch_run', [BindType::RUN => $this->run->id]), [
            'visibility' => Visibility::PRIVATE,
            'name' => Str::random(30),
            'description' => Str::random(255),
        ]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testGetMyRun(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $run_id = $this->run['id'];
        $response = $this->get($this->route('get_my_run_by_id', [BindType::RUN => $run_id]));
        $response->assertStatus(Response::HTTP_OK);
        $run = Run::find($run_id)->first();
        $this->assertForeignKeyIsExpectedID($this->user->id, $run->creator_id);
    }

    public function testGetSomeoneRun(): void
    {
        $targeted_user = factory(User::class)->create();
        Passport::actingAs($targeted_user);
        $this->run = factory(Run::class)->create([
            'visibility' => 'public',
        ]);
        $run_id = $this->run['id'];
        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_run_by_id', [
            BindType::USER => $targeted_user->id,
            BindType::RUN => $run_id,
        ]));
        dd($response->decodeResponseJson());
        $response->assertStatus(Response::HTTP_OK);
        $run = Run::find($run_id)->first();
        $this->assertForeignKeyIsExpectedID($targeted_user->id, $run->creator_id);
    }

    public function testGetAllMyRuns(): void
    {
        Passport::actingAs($this->user);

        for ($i = 0; $i < 20; $i++) {
            $run = factory(Run::class)->create([
                'visibility' => 'public',
            ]);
            $checkpoint = factory(CheckPoint::class)->create([
                'run_id' => $run['id'],
            ]);
            $user_run = $this->post(
                $this->route("post_user_run", [BindType::RUN => $run['id']])
            );
            $user_run_id = $user_run->decodeResponseJson('id');
            factory(Time::class)->create([
                'check_point_id' => $checkpoint['id'],
                'run_id' => $run['id'],
                "user_run_id" => $user_run_id,
            ]);
        }

        $response = $this->get($this->route('get_my_runs'));

        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(RunController::GET_PER_PAGE, count($response->decodeResponseJson('data')));
        $this->assertForeignKeyInArray($response->decodeResponseJson('data'), $this->user->id, Run::USER_FOREIGN_KEY);
        $checkpoint_json_part = Arr::get($response->decodeResponseJson('data')[0], 'checkpoints');
        $times_json_part = Arr::get($checkpoint_json_part[0], 'times')[0];
        $checkpoint_json_part = Arr::except($checkpoint_json_part[0], 'times');
        $response = Response::create($checkpoint_json_part);
        $test = TestResponse::fromBaseResponse($response);
        $test->assertJsonStructure((new CheckPoint)->getFillable());
        $response = Response::create($times_json_part);
        $test = TestResponse::fromBaseResponse($response);
        $test->assertJsonStructure((new Time)->getFillable());
    }

    public function testGetAllSomeoneRuns(): void
    {
        $targeted_user = factory(User::class)->create();
        Passport::actingAs($targeted_user);

        for ($i = 0; $i < 20; $i++) {
            $run = factory(Run::class)->create([
                'visibility' => 'public',
            ]);
            $user_run = $this->post(
                $this->route("post_user_run", [BindType::RUN => $run['id']])
            );
            $user_run_id = $user_run->decodeResponseJson('id');
            $checkpoint = factory(CheckPoint::class)->create([
                'run_id' => $run['id'],
            ]);
            factory(Time::class)->create([
                'check_point_id' => $checkpoint['id'],
                'run_id' => $run['id'],
                "user_run_id" => $user_run_id,
            ]);
        }

        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_runs', [BindType::USER => $targeted_user->id]));
        dd($response->decodeResponseJson());
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(RunController::GET_PER_PAGE, count($response->decodeResponseJson('data')));
        $this->assertForeignKeyInArray(
            $response->decodeResponseJson('data'),
            $targeted_user->id,
            Run::USER_FOREIGN_KEY
        );
        $checkpoint_json_part = Arr::get($response->decodeResponseJson('data')[0], 'checkpoints');
        $times_json_part = Arr::get($checkpoint_json_part[0], 'times')[0];
        $checkpoint_json_part = Arr::except($checkpoint_json_part[0], 'times');
        $response = Response::create($checkpoint_json_part);
        $test = TestResponse::fromBaseResponse($response);
        $test->assertJsonStructure((new CheckPoint)->getFillable());
        $response = Response::create($times_json_part);
        $test = TestResponse::fromBaseResponse($response);
        $test->assertJsonStructure((new Time)->getFillable());
    }

    public function testDeleteRun(): void
    {
        Passport::actingAs($this->user);
        $run = $this->post(
            $this->route('post_run'),
            ['name' => Str::random(30)]
        );
        $response = $this->delete($this->route("delete_run", [BindType::RUN => $run->decodeResponseJson('id')]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing("runs", $run->decodeResponseJson());
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
