<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

/**
 * Class LoginUserUnitTest
 *
 * @package Tests\Unit
 */
class LoginUserUnitTest extends TestCase
{
    use WithFaker;
    use RefreshDatabase;


    /** @var \App\Models\User */
    protected $user;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBadCredentialMustFail(): void
    {
        $this->request(
            [
                'email' => $this->faker->email,
                'password' => $this->faker->password
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
    }

    /**
     * @param string[] $data
     * @param int $status
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function request(array $data, int $status): TestResponse
    {
        $response = $this->json(Request::METHOD_POST, route('login'), $data);
        $response->assertStatus($status);

        return $response;
    }

    public function testBadPasswordMustFail(): void
    {
        $response = $this->request(
            [
                'email' => $this->user['email'],
                'password' => $this->faker->password
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJsonValidationErrors(['client_secret', 'client_id']);
    }

    public function testInvalidOAuthClient(): void
    {
        $response = $this->request(
            [
                'email' => $this->user->email,
                'password' => $this->user->getAuthPassword(),
                'client_id' => rand(0, 10),
                'client_secret' => str_random()
            ],
            Response::HTTP_UNPROCESSABLE_ENTITY
        );
        $response->assertJsonValidationErrors(['email']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->make();
        User::create($this->user->toArray());
    }
}
