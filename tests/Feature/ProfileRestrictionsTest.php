<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\Preferences;
use App\Enums\Restriction;
use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProfileRestrictionsTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    protected $user1;

    /** @var \App\Models\User */
    protected $user2;

    public function testModifyRestrictions(): void
    {
        $response = $this->patch(route('patch_my_profile_restrictions'), [
            "city" => Restriction::PUBLIC,
            "description" => Restriction::FRIENDS,
            "birthday" => Restriction::FRIENDS_FOLLOWERS,
            "nomination_preference" => Preferences::NICKNAME,
        ]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->assertDatabaseHas('profile_restrictions', [
            'user_id' => $this->user1->id,
            "city" => Restriction::PUBLIC,
            "description" => Restriction::FRIENDS,
            "birthday" => Restriction::FRIENDS_FOLLOWERS,
            "nomination_preference" => Preferences::NICKNAME,
        ]);
    }

    public function testGetRestrictionsSomeone(): void
    {
        $response = $this->get(route('get_user_profile_restrictions', ["user" => $this->user2->id]));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'user_id' => $this->user2->id,
            'city' => Restriction::PUBLIC,
            'description' => Restriction::FRIENDS_FOLLOWERS,
            'birthday' => Restriction::PRIVATE,
            "nomination_preference" => Preferences::FULL_NAME,
        ]);
    }

    public function testGetRestrictionsMe(): void
    {
        $response = $this->get(route('get_my_profile_restrictions'));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'user_id' => $this->user1->id,
            "city" => Restriction::FRIENDS,
            "description" => Restriction::FRIENDS,
            "birthday" => Restriction::FRIENDS,
            "nomination_preference" => Preferences::FULL_NAME,
        ]);
    }

    public function testGetRestrictedProfile(): void
    {
        $response = $this->get(route('get_user_profile', ['user' => $this->user2->id]));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            "nick_name" => null,
            "city" => "Sospel",
            "description" => null,
            "birthday" => null,
        ]);
        $expectTo = [
            'profile' => (new UserProfile)->getFillable(),
        ];
        $userProperties = array_diff((new User)->getFillable(), (new User)->getHidden());
        $expectTo = array_merge($expectTo, $userProperties);
        $response->assertJsonStructure($expectTo);
        /////////////////////////////////////////////////////////////////////////////
        $this->post(route('post_follow', ['user' => $this->user2->id]));
        $response = $this->get(route('get_user_profile', ['user' => $this->user2->id]));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            "first_name" => $this->user2->first_name,
            "last_name" => $this->user2->last_name,
            "nick_name" => null,
            "city" => "Sospel",
            "description" => 'Ma description test.',
            "birthday" => null,
        ]);
        $expectTo = [
            'profile' => (new UserProfile)->getFillable(),
        ];
        $userProperties = array_diff((new User)->getFillable(), (new User)->getHidden());
        $expectTo = array_merge($expectTo, $userProperties);
        $response->assertJsonStructure($expectTo);
    }

    public function testChangeNominationPreference(): void
    {
        Passport::actingAs($this->user2);
        $response = $this->patch(route('patch_user'), ['nick_name' => "Cactus"]);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
        $this->patch(route('patch_my_profile_restrictions'), [
            'city' => Restriction::PRIVATE,
            'description' => Restriction::PRIVATE,
            'birthday' => Restriction::PRIVATE,
            "nomination_preference" => Preferences::NICKNAME,
        ]);
        Passport::actingAs($this->user1);
        $response = $this->get(route('get_user_profile', ['user' => $this->user2->id]));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJsonFragment([
            "first_name" => null,
            "last_name" => null,
            "nick_name" => $this->user2->nick_name,
            "city" => null,
            "description" => null,
            "birthday" => null,
        ]);
        $expectTo = [
            'profile' => (new UserProfile)->getFillable(),
        ];
        $userProperties = array_diff((new User)->getFillable(), (new User)->getHidden());
        $expectTo = array_merge($expectTo, $userProperties);
        $response->assertJsonStructure($expectTo);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();

        Passport::actingAs($this->user2);
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
        Passport::actingAs($this->user1);
    }
}
