<?php

namespace Tests\Feature;

use App\Models\Run;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Tests\TestCase;

class ShareRunTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testCreateShareOfARun()
    {
        $run = factory(Run::class)->create();
        $user = factory(User::class)->create();
        dd($user);

        //Auth::login(Passport::actingAs($user));
        $response = $this->json(
            Request::METHOD_POST,
            route('post_share_run'),
            ['id' => $run->id]
        );
        $response->assertStatus(Response::HTTP_CREATED);
    }
}
