<?php

namespace Tests\Feature;

use App\Enums\Restriction;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardTest extends TestCase
{
    private $user;

    private $dash;

    protected function setUp()
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $this->dash = factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
    }

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

    public function testGetRestriction()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_dashboard_restriction'));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(1, count($response->decodeResponseJson()));
    }

    public function testChangeRestriction()
    {
        Passport::actingAs($this->user);
        $response = $this->patch(route("patch_dashboard_restriction"), ['restriction' => Restriction::PUBLIC]);
        $response->assertStatus(Response::HTTP_OK);
    }
}
