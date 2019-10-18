<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\BindType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class UuidUnitTest extends TestCase
{
    use RefreshDatabase;

    public function testInvalidUuidMustThrowHTTP422(): void
    {
        Passport::actingAs(factory(User::class)->create());

        $response = $this->get($this->route("get_user_profile", [BindType::USER => 42]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testValidUuidButNotInDBMustThrowHTTP422(): void
    {
        Passport::actingAs(factory(User::class)->create());

        $response = $this->get($this->route("get_user_profile", [BindType::USER => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
