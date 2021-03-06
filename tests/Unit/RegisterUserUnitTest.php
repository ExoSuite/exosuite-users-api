<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class RegisterUserUnitTest
 *
 * @package Tests\Unit
 */
class RegisterUserUnitTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /**
     * Assert if error will be sent
     *
     * @return void
     */
    public function testRegisterUserWithInvalidData(): void
    {
        $this->request(['first_name', 'last_name', 'password', 'email']);
    }


    /**
     * A basic test example.
     *
     * @return void
     */
    public function testLoopWithInvalidData(): void
    {
        /** @var \App\Models\User $userData */
        $user = factory(User::class)->make();
        /** @var array $userData */
        $userData = $user->toArray();
        $userData['password'] = $user->password;
        $userData['password_confirmation'] = $user->password;
        $userData = Arr::except($userData, ['password_confirmation']);

        $data = array_keys($userData);

        foreach ($userData as $key) {
            $this->request($data, $data);
            $data = array_diff($data, [$key]);
        }

        $data = ['password_confirmation' => $userData['password']];
        $this->request(['password'], $data);

        $userData['password_confirmation'] = Str::random();
        $this->request(['password'], $userData);
    }

    public function testBadPasswordFormat(): void
    {
        /** @var \App\Models\User $userData */
        $user = factory(User::class)->make();
        /** @var array $userData */
        $userData = $user->toArray();
        $userData['password'] = "aozkeaope";
        $userData['password_confirmation'] = "aozkeaope";

        $response = $this->json(Request::METHOD_POST, route('register'), $userData);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals($response->decodeResponseJson("errors")["password"][0], trans("passwords.bad"));
    }

    /**
     * @param string[] $expected
     * @param string[] $data
     */
    private function request(array $expected, array $data = []): void
    {
        $response = $this->json(Request::METHOD_POST, route('register'), $data);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJsonValidationErrors($expected);
    }
}
