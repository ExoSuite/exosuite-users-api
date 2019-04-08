<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Webpatser\Uuid\Uuid;

class ProfileRestrictionsUnitTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    protected $user1;

    /** @var \App\Models\User */
    protected $user2;

    public function testChangeCityRestrictionsWithWrongValues(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->patch(route('patch_my_profile_restrictions'), [
            "city" => Str::random(),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testChangeDescriptionRestrictionsWithWrongValues(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->patch(route('patch_my_profile_restrictions'), [
            "description" => Str::random(),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testChangeBirthdayRestrictionsWithWrongValues(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->patch(route('patch_my_profile_restrictions'), [
            "birthday" => Str::random(),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testChangeNomPreferenceWithWrongValues(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->patch(route('patch_my_profile_restrictions'), [
            "nomination_preference" => Str::random(),
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetRestrictionsNobody(): void
    {
        Passport::actingAs($this->user1);
        $response = $this->get(route('get_user_profile_restrictions', ["user" => Uuid::generate()->string]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testGetProfileNobody(): void
    {
        Passport::actingAs($this->user1);
        $response1 = $this->get(route('get_user_profile', ['user' => Uuid::generate()->string]));
        $response1->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->user1 = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }
}
