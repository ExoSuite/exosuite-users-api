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

    public function testGetMyRestriction(): void
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_dashboard_restriction'));
        $response->assertStatus(Response::HTTP_OK);
        $this->assertEquals(2, count($response->decodeResponseJson()));
    }

    public function testChangeVisibility(): void
    {
        Passport::actingAs($this->user);
        $response = $this->patch(route('patch_dashboard_restriction'), [
            "restriction_field" => "visibility",
            "restriction_level" => Restriction::FRIENDS_FOLLOWERS]);
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testChangeWrintingRestriction(): void
    {
        Passport::actingAs($this->user);
        $response = $this->patch(route('patch_dashboard_restriction'), [
            "restriction_field" => "writing_restriction",
            "restriction_level" => Restriction::FRIENDS_FOLLOWERS]);
        $response->assertStatus(Response::HTTP_OK);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        factory(Dashboard::class)->create(['owner_id' => $this->user->id]);
    }
}
