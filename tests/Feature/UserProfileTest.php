<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserProfile;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserProfileTest
 * @package Tests\Feature
 */
class UserProfileTest extends TestCase
{

    /**
     * @var null
     */
    private static $user = null;

    /**
     *
     */
    public function testGetProfile()
    {
        $response = $this->get(route('get_user_profile', ["user" => self::$user->id]));
        $response->assertStatus(Response::HTTP_OK);
        $expectTo = [
            'profile' => (new UserProfile())->getFillable()
        ];
        $userProperties = array_diff((new User())->getFillable(), (new User())->getHidden());
        $expectTo = array_merge($expectTo, $userProperties);
        $response->assertJsonStructure($expectTo);
    }

    /**
     *
     */
    public function testPatchProfileDescription()
    {
        $data = [
            'description' => str_random()
        ];

        $response = $this->patch(route('patch_user_profile'), $data);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     *
     */
    public function testPatchProfileCity()
    {
        $data = [
            'city' => str_random()
        ];

        $response = $this->patch(route('patch_user_profile'), $data);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     *
     */
    public function testPatchProfileBirthday()
    {
        $data = [
            'birthday' => Carbon::now()->format("Y-m-d")
        ];

        $response = $this->patch(route('patch_user_profile'), $data);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     *
     */
    protected function setUp()
    {
        parent::setUp();
        if (!self::$user) {
            self::$user = factory(User::class)->create();
        }
        Passport::actingAs(self::$user);
    }
}
