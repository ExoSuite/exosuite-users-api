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
        $this->dash = factory(Dashboard::class)->create();
        $this->dash['owner_id'] = $this->user->id;
        $this->dash['restriction'] = Restriction::FRIENDS;
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testGetDashboardId()
    {
        Passport::actingAs($this->user);
        $response = $this->get($this->route('getSomeoneDashboardId'), ['id' => $this->user->id]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testGetRestriction()
    {
        Passport::actingAs($this->user);
        $response = $this->get($this->route('getRestriction'));
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testChangeRestriction()
    {
        Passport::actingAs($this->user);
        $response = $this->patch($this->route("changeRestriction"), ['restriction' => Restriction::PUBLIC]);
        $response->assertStatus(Response::HTTP_OK);
    }
}
