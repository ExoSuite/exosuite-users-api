<?php declare(strict_types=1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\Run;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class RunTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /** @var \App\Models\Run */
    private $run;

    public function testCreateRunWithoutName(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_run')
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateRunWithBadName(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_run'),
            ['name' => Str::random(300)]
        );
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateRunWithBadDescription(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            'name' => Str::random(30),
            'description' => Str::random(300),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testCreateRunWithBadVisibility(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post($this->route('post_run'), [
            'name' => Str::random(30),
            'description' => Str::random(255),
            'visibility' => "faulty_visibility",
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateBadRun(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route('patch_run', [BindType::RUN => Uuid::generate()->string]), [
            'name' => Str::random(30),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateRunWithBadName(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route('patch_run', [BindType::RUN => $this->run->id]), [
            'name' => Str::random(300),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateRunWithBadDescription(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route('patch_run', [BindType::RUN => $this->run->id]), [
            'description' => Str::random(300),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testUpdateRunWithBadVisibility(): void
    {
        Passport::actingAs($this->user);
        $this->run = factory(Run::class)->create();
        $response = $this->patch($this->route('patch_run', [$this->run->id]), [
            'visibility' => "faulty_visibility",
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetMyBadRun(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_run'),
            ['name' => Str::random(30)]
        );
        $response = $this->get($this->route('get_my_run_by_id', [BindType::RUN => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetSomeoneBadRun(): void
    {
        $targeted_user = factory(User::class)->create();
        Passport::actingAs($targeted_user);
        $response = $this->post(
            $this->route('post_run'),
            ['name' => Str::random(30)]
        );
        Passport::actingAs($this->user);
        $response = $this->get($this->route('get_run_by_id', [BindType::USER => $targeted_user->id, BindType::RUN =>
            Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testDeleteBadRun(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_run'),
            ['name' => Str::random(30)]
        );
        $response = $this->delete($this->route("delete_run", [BindType::RUN => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
