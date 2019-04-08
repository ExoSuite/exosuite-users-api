<?php declare(strict_types = 1);

namespace Tests\Feature;

use App\Enums\TokenScope;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserProfilePictureTest
 *
 * @package Tests\Feature
 */
class UserProfilePictureTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    public function testStoreUserProfilePictureAvatar(): void
    {
        $response = $this->storeUserProfilePictureAvatar();
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertHeader('Location', $this->route(
            'get_picture_avatar',
            ['user' => $this->user->id],
            true
        ));
    }

    public function testGetUserProfilePictureAvatar(): void
    {
        $this->storeUserProfilePictureAvatar();
        Passport::actingAs($this->user, [TokenScope::VIEW_PICTURE]);
        $response = $this->get(route('get_picture_avatar', ['user' => $this->user]));
        $response->assertStatus(Response::HTTP_OK);
    }

    public function testStoreUserProfilePictureCover(): void
    {
        $response = $this->storeUserProfilePictureCover();
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertHeader('Location', $this->route(
            'get_picture_cover',
            ['user' => $this->user],
            true
        ));
    }

    public function testGetUserProfilePictureCover(): void
    {
        $this->storeUserProfilePictureCover();
        Passport::actingAs($this->user, [TokenScope::VIEW_PICTURE]);
        $response = $this->get(route('get_picture_cover', ['user' => $this->user]));
        $response->assertStatus(Response::HTTP_OK);
    }

    private function storeUserProfilePictureAvatar(): TestResponse
    {
        Passport::actingAs($this->user);

        return $this->post(
            $this->route('post_picture_avatar'),
            ['picture' => UploadedFile::fake()->image('avatar.jpg', 142, 142)],
            ['Content-Type' => 'multipart/form-data']
        );
    }

    private function storeUserProfilePictureCover(): TestResponse
    {
        Passport::actingAs($this->user);

        return $this->post(
            $this->route('post_picture_cover', ['user' => $this->user]),
            ['picture' => UploadedFile::fake()->image('cover.jpg', 1920, 640)],
            ['Content-Type' => 'multipart/form-data']
        );
    }
}
