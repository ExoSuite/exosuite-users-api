<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Enums\Restriction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class DashboardUnitTest
 *
 * @package Tests\Unit
 */
class DashboardUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /**
     * A basic test example.
     *
     * @return void
     * @throws \Exception
     */
    public function testGetIdWithWrongUser(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_dashboard_id', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testChangeRestrictionWithWrongField(): void
    {
        Passport::actingAs($this->user);
        $response = $this->patch(route('patch_dashboard_restriction', [
            'user' => $this->user->id,
        ]), ["restriction_field" => "blas",
            "restriction_level" => Restriction::FRIENDS_FOLLOWERS]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testChangeRestrictionWithWrongLevel(): void
    {
        Passport::actingAs($this->user);
        $response = $this->patch(route('patch_dashboard_restriction', [
            'user' => $this->user->id,
        ]), ["restriction_field" => "writing_restriction",
            "restriction_level" => "test"]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }
}
