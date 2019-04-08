<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\Preferences;
use App\Enums\Restriction;
use App\Models\User;
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
        Passport::actingAs($this->user1);
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
        Passport::actingAs($this->user1);
        $response = $this->get(route('get_user_profile_restrictions', ["user" => $this->user2->id]));
        $response->assertStatus(Response::HTTP_OK);
        $response->assertJson([
            'user_id' => $this->user2->id,
            "city" => Restriction::FRIENDS,
            "description" => Restriction::FRIENDS,
            "birthday" => Restriction::FRIENDS,
            "nomination_preference" => Preferences::FULL_NAME,
        ]);
    }

    public function testGetRestrictionsMe(): void
    {
        Passport::actingAs($this->user1);
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

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }
}
