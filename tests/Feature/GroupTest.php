<?php

namespace Tests\Feature;

use App\Http\Controllers\GroupController;
use App\Models\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    public function testCreateGroup()
    {
        $user1 = factory(User::class)->create();
        $user2 = factory(User::class)->create();
        Passport::actingAs($user1);
        //$this->post($this);
    }
}
