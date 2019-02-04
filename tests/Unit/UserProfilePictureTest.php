<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserProfilePictureTest
 * @package Tests\Unit
 */
class UserProfilePictureTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @var
     */
    private $user;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
    }

    /**
     *
     */
    public function testStoreUserProfilePictureAvatar()
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_picture_avatar', ["user" => $this->user]),
            ['picture' => UploadedFile::fake()->image('avatar.jpg', 50, 50)],
            ['Content-Type' => 'multipart/form-data']
        );
        $response->assertJsonValidationErrors(['picture']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testGetUserProfilePictureAvatar()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_picture_avatar', ["user" => $this->user]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testStoreUserProfilePictureCover()
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            $this->route('post_picture_cover', ["user" => $this->user]),
            ['picture' => UploadedFile::fake()->image('cover.jpg', 1920, 640)->size(10500)],
            ['Content-Type' => 'multipart/form-data']
        );
        $response->assertJsonValidationErrors(['picture']);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testGetUserProfilePictureCover()
    {
        Passport::actingAs($this->user);
        $response = $this->get(route('get_picture_cover', ["user" => $this->user]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
