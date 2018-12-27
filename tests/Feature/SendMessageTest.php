<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class SendMessageTest
 * @package Tests\Feature
 */
class SendMessageTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var User
     */
    private $user2;

    public function testCreateMessage()
    {
        Passport::actingAs($this->user);
        $response = $this->post(
            route("post_message", [], false),
            ["contents" => str_random()]
        );
        $response->assertJsonStructure((new Message())->getFillable());
    }

    /**
     *
     */
    public function testExample()
    {
        $this->assertTrue(true);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }
}
