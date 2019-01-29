<?php

namespace Tests\Unit;

use App\Models\User;
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
    /**
     * @var
     */
    static $user;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        if (!self::$user) {
            self::$user = factory(User::class)->create();
        }
    }

    /**
     *
     */
    public function testStoreUserProfilePictureAvatar()
    {
        Passport::actingAs(self::$user);
        $response = $this->post(
            $this->route('post_picture_avatar', ["user" => self::$user->id]),
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
        Passport::actingAs(self::$user);
        $response = $this->get(route('get_picture_avatar', ["user" => self::$user->id]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *
     */
    public function testStoreUserProfilePictureCover()
    {
        Passport::actingAs(self::$user);
        $response = $this->post(
            $this->route('post_picture_cover', ["user" => self::$user->id]),
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
        Passport::actingAs(self::$user);
        $response = $this->get(route('get_picture_cover', ["user" => self::$user->id]));
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
