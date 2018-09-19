<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use App\Models\User;
use Laravel\Passport\Passport;

class UserProfileUnitTest extends TestCase
{
    private $user = null;

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->user = Passport::actingAs($this->user);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateProfileTwoTimesMustFail()
    {
        $response = $this->post(route('user_profile_create'), [
            'description' => str_random()
        ]);
        $response->assertStatus(Response::HTTP_CREATED);

        $response = $this->post(route('user_profile_create'), [
            'description' => str_random()
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
