<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Laravel\Passport\Passport;
use Tests\TestCase;

/**
 * Class UserProfilePictureTest
 * @package Tests\Feature
 */
class UserProfilePictureTest extends TestCase
{
    /**
     * @var User
     */
    static $user;

    /**
     *
     */
    public function setUp()
    {
        parent::setUp();
        if (!self::$user)
            self::$user = factory(User::class)->create();
    }

    /**
     *
     */
    public function testStoreUserProfilePictureAvatar()
    {
        Passport::actingAs(self::$user);
        $response = $this->post($this->route('post_picture_avatar', ["user" => self::$user->id]),
            ['picture' => UploadedFile::fake()->image('avatar.jpg', 142, 142)],
            ['Content-Type' => 'multipart/form-data']);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     *
     */
    public function testGetUserProfilePictureAvatar()
    {
        Passport::actingAs(self::$user);
        $response = $this->get(route('get_picture_avatar', ["user" => self::$user->id]));
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     *
     */
    public function testStoreUserProfilePictureCover()
    {
        Passport::actingAs(self::$user);
        $response = $this->post($this->route('post_picture_cover', ["user" => self::$user->id]),
            ['picture' => UploadedFile::fake()->image('cover.jpg', 1920, 640)],
            ['Content-Type' => 'multipart/form-data']);
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    /**
     *
     */
    public function testGetUserProfilePictureCover()
    {
        Passport::actingAs(self::$user);
        $response = $this->get(route('get_picture_cover', ["user" => self::$user->id]));
        $response->assertStatus(Response::HTTP_OK);
    }
}
