<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\Restriction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserProfileTest
 *
 * @package Tests\Feature
 */
class UserProfileTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User $user */
    private static $user = null;

    public function testGetProfile(): void
    {
        $user2 = factory(User::class)->create();
        Passport::actingAs($user2);
        $this->patch(route('patch_user_profile'), [
            'description' => 'Ma description test.',
            'city' => 'Sospel',
            'birthday' => '1997-10-20',
        ]);
        $this->patch(route('patch_my_profile_restrictions'), [
            'city' => Restriction::PUBLIC,
            'description' => Restriction::FRIENDS_FOLLOWERS,
            'birthday' => Restriction::PRIVATE,
        ]);
        Passport::actingAs(self::$user);
        $response1 = $this->get(route('get_user_profile', ['user' => $user2->id]));
        $response1->assertStatus(Response::HTTP_OK);
        $response1->assertJson([
            'name' => $user2->first_name . ' ' . $user2->last_name,
            "city" => "Sospel",
            "description" => null,
            "birthday" => null,
        ]);
        $this->post(route('post_follow', ['user' => $user2->id]));
        $response1 = $this->get(route('get_user_profile', ['user' => $user2->id]));
        $response1->assertStatus(Response::HTTP_OK);
        $response1->assertJson([
            'name' => $user2->first_name . ' ' . $user2->last_name,
            "city" => "Sospel",
            "description" => 'Ma description test.',
            "birthday" => null,
        ]);
    }

    public function testPatchProfileDescription(): void
    {
        $data = [
            'description' => Str::random(),
        ];

        $response = $this->patch(route('patch_user_profile'), $data);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testPatchProfileCity(): void
    {
        $data = [
            'city' => Str::random(),
        ];

        $response = $this->patch(route('patch_user_profile'), $data);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function testPatchProfileBirthday(): void
    {
        $data = [
            'birthday' => Carbon::now()->format('Y-m-d'),
        ];

        $response = $this->patch(route('patch_user_profile'), $data);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (self::$user === null) {
            self::$user = factory(User::class)->create();
        }

        Passport::actingAs(self::$user);
    }
}
