<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserProfileTest
 * @package Tests\Feature
 */
class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var null
     */
    private static $user = null;

    public function testGetProfile(): void
    {
        $response = $this->get(route('get_user_profile', ["user" => self::$user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $expectTo = [
            'profile' => (new UserProfile)->getFillable()
        ];
        $userProperties = array_diff((new User)->getFillable(), (new User)->getHidden());
        $expectTo = array_merge($expectTo, $userProperties);
        $response->assertJsonStructure($expectTo);
    }

    public function testPatchProfileDescription(): void
    {
        $data = [
            'description' => str_random()
        ];

        $response = $this->patch(route('patch_user_profile'), $data);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testPatchProfileCity(): void
    {
        $data = [
            'city' => str_random()
        ];

        $response = $this->patch(route('patch_user_profile'), $data);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testPatchProfileBirthday(): void
    {
        $data = [
            'birthday' => Carbon::now()->format("Y-m-d")
        ];

        $response = $this->patch(route('patch_user_profile'), $data);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!self::$user) {
            self::$user = factory(User::class)->create();
        }

        Passport::actingAs(self::$user);
    }
}
