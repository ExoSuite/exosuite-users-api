<?php

use Faker\Generator as Faker;
use App\Models\Group;
use Webpatser\Uuid\Uuid;

$factory->define(Group::class, function (Faker $faker) {
    return [
        "name" => str_random(10),
        "id" => Uuid::generate()->string
    ];
});
