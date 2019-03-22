<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\BindType;
use App\Enums\Visibility;
use App\Http\Controllers\Run\RunController;
use App\Models\Run;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
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
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUpdateRunDescription(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route('patch_run', [BindType::RUN => $this->run->id]), [
            'description' => Str::random(255),
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUpdateRunVisibility(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route('patch_run', [BindType::RUN => $this->run->id]), [
            'visibility' => Visibility::PRIVATE,
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
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
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testGetMyRun(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_run'),
            ['name' => Str::random(30)]
        );
        $run_id = $response->decodeResponseJson('id');
        $response = $this->get($this->route('get_my_run_by_id', [BindType::RUN => $run_id]));
        $response->assertStatus(Response::HTTP_OK);
        $run = Run::find($run_id);
        $this->assertForeignKeyIsExpectedID($this->user->id, $run->creator_id);
    }

    public function testGetSomeoneRun(): void
    {
        $targeted_user = factory(User::class)->create();
        Passport::actingAs($targeted_user);
        $response = $this->post(
            $this->route('post_run'),
            ['name' => Str::random(30)]
        );
        $run_id = $response->decodeResponseJson('id');
        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_run_by_id', [BindType::USER => $targeted_user->id, BindType::RUN =>
            $run_id]));
        $response->assertStatus(Response::HTTP_OK);
        $run = Run::find($run_id);
        $this->assertForeignKeyIsExpectedID($targeted_user->id, $run->creator_id);
    }

    public function testGetAllMyRuns(): void
    {
        Passport::actingAs($this->user);

        for ($i = 0; $i < 20; $i++) {
            factory(Run::class)->create();
        }

        $response = $this->get($this->route('get_my_runs'));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(RunController::GET_PER_PAGE, count($response->decodeResponseJson('data')));
        $this->assertForeignKeyInArray($response->decodeResponseJson('data'), $this->user->id, Run::USER_FOREIGN_KEY);
    }

    public function testGetAllSomeoneRuns(): void
    {
        $targeted_user = factory(User::class)->create();
        Passport::actingAs($targeted_user);

        for ($i = 0; $i < 20; $i++) {
            $response = $this->post(
                $this->route('post_run'),
                ['name' => Str::random(30), 'visibility' => Visibility::PUBLIC]
            );
        }

        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_runs', [BindType::USER => $targeted_user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(RunController::GET_PER_PAGE, count($response->decodeResponseJson('data')));
        $this->assertForeignKeyInArray(
            $response->decodeResponseJson('data'),
            $targeted_user->id,
            Run::USER_FOREIGN_KEY
        );
    }

    public function testDeleteRun(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_run'),
            ['name' => Str::random(30)]
        );
        $run = $response->decodeResponseJson();
        $response = $this->delete($this->route("delete_run", [BindType::RUN => $run['id']]));
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseMissing("runs", $run);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
