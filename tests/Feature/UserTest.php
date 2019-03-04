<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserTest
 *
 * @package Tests\Feature
 */
class UserTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @var \App\Models\User */
    private $user;

    /** @var string */
    private $userPassword = null;

    public function testLoginMustReturnTokens(): void
    {
        $response = $this->json(
            Request::METHOD_POST,
            route('login'),
            [
                'email' => $this->user->email,
                'password' => $this->userPassword,
                'client_id' => 2,
                'client_secret' => Client::whereId(2)->first()->secret,
            ]
        );
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonStructure(
            [
                'token_type',
                'expires_in',
                'access_token',
                'refresh_token',
            ]
        );
    }

    public function testGetPersonalInfos(): void
    {
        Passport::actingAs(factory(User::class)->create());

        $response = $this->get(
            route('get_user')
        );
        $response->assertStatus(Response::HTTP_OK);
        $expectTo = array_diff((new User)->getFillable(), (new User)->getHidden());
        $expectTo['profile'] = (new UserProfile)->getFillable();
        $response->assertJsonStructure($expectTo);
    }

    public function testUpdateUserFirstName(): void
    {
        Passport::actingAs(factory(User::class)->create());

        $response = $this->patch(route('patch_user'), ['first_name', $this->faker->firstName]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUpdateUserLastName(): void
    {
        Passport::actingAs(factory(User::class)->create());

        $response = $this->patch(route('patch_user'), ['last_name', $this->faker->lastName]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testUpdateUserNickname(): void
    {
        Passport::actingAs(factory(User::class)->create());

        $response = $this->patch(route('patch_user'), ['nick_name', $this->faker->name]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('passport:install');
        /** @var \App\Models\User $userData */
        $user = factory(User::class)->make();
        /** @var array $userData */
        $userData = $user->toArray();
        $userData['password'] = $user->password;
        $this->userPassword = $user->password;

        $this->user = User::create($userData);
        $this->assertTrue(UserProfile::whereId($this->user->id)->first()->id === $this->user->id);
    }
}
