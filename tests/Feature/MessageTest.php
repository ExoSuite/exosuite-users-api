<?php

namespace Tests\Feature;

use App\Events\NewMessageEvent;
use App\Models\Group;
use App\Models\Message;
use Illuminate\Http\Response;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Webpatser\Uuid\Uuid;

/**
 * Class MessageTest
 * @package Tests\Feature
 */
class MessageTest extends TestCase
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var User
     */
    private $user2;

    public function testSimpleMessage()
    {
        //Event::fake();
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
        $group = factory(Group::class)->create();
        $fake_uuid = $group->id;

        Passport::actingAs($this->user);
        $response = $this->post(route("post_message", ["group_id" => $fake_uuid], false), ["contents" => str_random(10)]);
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Message())->getFillable());
    }

    public function testCreateMessage()
    {
        Event::fake();
        $group = factory(Group::class)->create();
        $array = $this->user->toArray();
        $array["user_id"] = $array["id"];
        $array2 = $this->user2->toArray();
        $array2["user_id"] = $array2["id"];
        $group->groupMembers()->createMany([
            $array, $array2
        ]);
        Passport::actingAs($this->user);
        $response = $this->post(
            route("post_message", ["group_id" => $group->id], false),
            ["contents" => str_random()]
        );
        dd($response->decodeResponseJson());
        $response->assertStatus(Response::HTTP_CREATED);
        $response->assertJsonStructure((new Message())->getFillable());
        Event::assertDispatched(NewMessageEvent::class, 1);
    }

    protected function setUp()
    {
        parent::setUp();
        $this->user = factory(User::class)->create();
        $this->user2 = factory(User::class)->create();
    }
}
