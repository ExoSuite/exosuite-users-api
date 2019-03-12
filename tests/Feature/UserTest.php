<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\ClientRepository;
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
        $clientRepository = new ClientRepository;
        $client = $clientRepository->createPasswordGrantClient(null, Str::random(), "http://localhost");

        $response = $this->json(
            Request::METHOD_POST,
            route('login'),
            [
                'email' => $this->user->email,
                'password' => $this->userPassword,
                'client_id' => $client->id,
                'client_secret' => $client->secret,
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
        Passport::actingAs(factory(User::class)->create(['nick_name' => Str::random()]));

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
