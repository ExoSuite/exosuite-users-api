<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class PreFlightCheckTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;

    public function testEmailIsAvailableForRegistering(): void
    {
        $response = $this->post($this->route('preflight_is_mail_available'), ['email' => $this->faker->email]);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testEmailIsNotAvailableForRegistering(): void
    {
        $email = $this->faker->email;
        factory(User::class)->create(['email' => $email]);
        $response = $this->post($this->route('preflight_is_mail_available'), ['email' => $email]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
