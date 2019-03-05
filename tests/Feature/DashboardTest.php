<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\Restriction;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class DashboardTest
 *
 * @package Tests\Feature
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetDashboardId(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_dashboard_id', ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1, count($response->decodeResponseJson()));
    }

    public function testGetRestriction(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_dashboard_restriction', ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1, count($response->decodeResponseJson()));
    }

    public function testChangeRestriction(): void
    {
        Passport::actingAs($this->user);
        $response = $this->patch(route('patch_dashboard_restriction', [
            'user' => $this->user->id,
        ]), ['restriction' => Restriction::PUBLIC]);
        $response->assertStatus(Response::HTTP_OK);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
    }
}
