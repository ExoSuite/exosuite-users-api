<?php

use App\Enums\Restriction;
use App\Models\Dashboard;
use Faker\Generator as Faker;

$factory->define(Dashboard::class, function (Faker $faker) {
    return [
        "restriction" => Restriction::FRIENDS,
        "owner_id" => \Webpatser\Uuid\Uuid::generate()->string
    ];
});
