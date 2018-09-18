<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserProfileTest extends TestCase
{

    private $user = null;

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory( User::class )->create();
        $this->user = Passport::actingAs( $this->user );
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateProfile()
    {
        $data = [
            'description' => str_random()
        ];

        $response = $this->post( route( 'user_profile_create' ), $data );
        $response->assertStatus( Response::HTTP_CREATED );

        $this->assertDatabaseHas( 'user_profiles', $data );
    }
}
