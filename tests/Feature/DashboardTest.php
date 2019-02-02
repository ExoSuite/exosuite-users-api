<?php

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
 * @package Tests\Feature
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;
    /**
     * @var
     */
    private $user;

    /**
     * @var
     */
    private $dash;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetDashboardId()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_dashboard_id', ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1, count($response->decodeResponseJson()));
    }

    /**
     *
     */
    public function testGetRestriction()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_dashboard_restriction', ['user' => $this->user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1, count($response->decodeResponseJson()));
    }

    /**
     *
     */
    public function testChangeRestriction()
    {
        Passport::actingAs($this->user);
        $response = $this->patch(route("patch_dashboard_restriction", [
            'user' => $this->user->id
        ]), ['restriction' => Restriction::PUBLIC]);
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
    }
}
