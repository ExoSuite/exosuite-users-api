<?php declare(strict_types = 1);

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserProfilePictureTest
 *
 * @package Tests\Unit
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
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_picture_avatar', ['user' => $this->user]),
            ['picture' => UploadedFile::fake()->image('avatar.jpg', 50, 50)],
            ['Content-Type' => 'multipart/form-data']
        );
        $response->assertJsonValidationErrors(['picture']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testStoreUserProfilePictureCover(): void
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_picture_cover', ['user' => $this->user]),
            ['picture' => UploadedFile::fake()->image('cover.jpg', 1920, 640)->size(10500)],
            ['Content-Type' => 'multipart/form-data']
        );
        $response->assertJsonValidationErrors(['picture']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
