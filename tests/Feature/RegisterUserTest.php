<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class RegisterUserTest
 *
 * @package Tests\Feature
 */
class RegisterUserTest extends TestCase
{
    use RefreshDatabase;

    public function testRegisterUserWithReturnedUser(): void
    {
        $this->testRegisterUser(true);
    }

    /**
     * Register an user
     *
     * @param bool $with_user
     * @param bool $with_nick_name
     * @return void
     */
    public function testRegisterUser(bool $with_user = false, bool $with_nick_name = false): void
    {
        /** @var \App\Models\User $user */
        $user = factory(User::class)->make();
        /** @var array $userData */
        $userData = $user->toArray();
        $userData['password'] = $user->password;
        $userData['password_confirmation'] = $user->password;

        if ($with_user) {
            $userData['with_user'] = true;
        }

        if ($with_nick_name) {
            $userData['nick_name'] = Str::random();
        }

        $response = $this->json(Request::METHOD_POST, route('register'), $userData);
        $response->assertStatus(Response::HTTP_CREATED);
        $userData = Arr::except($userData, ['password_confirmation', 'password', 'with_user']);

        if ($with_user) {
            $structure = [
                'email',
                'id',
                'first_name',
                'created_at',
                'updated_at',
                'last_name',
            ];

            if ($with_nick_name) {
                array_push($structure, 'nick_name');
            }

            $response->assertJsonStructure($structure);
        }

        $this->assertDatabaseHas('users', $userData);
    }

    public function testRegisterUserWithNickNameWithReturnedUser(): void
    {
        $this->testRegisterUser(true, true);
    }
}
