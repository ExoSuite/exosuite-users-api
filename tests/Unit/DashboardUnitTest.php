<?php

namespace Tests\Unit;

use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

/**
 * Class DashboardUnitTest
 * @package Tests\Unit
 */
class DashboardUnitTest extends TestCase
{
    /**
     * @var
     */
    private $user;

    /**
     * @var
     */
    private $dash;

    /**
     *
     */
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
     * @throws \Exception
     */
    public function testGetIdWithWrongUser()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_dashboard_id', ['user' => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testChangeRestrictionWithWrongValue()
    {
        Passport::actingAs($this->user);
        $response = $this->patch(route('patch_dashboard_restriction', ['user' => $this->user->id]), [
            'restriction' => 'wrong_value'
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
