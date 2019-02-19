<?php

use App\Models\Group;
use Faker\Generator as Faker;
use Webpatser\Uuid\Uuid;

$factory->define(Group::class, function (Faker $faker) {
    $faker->locale = \Faker\Factory::DEFAULT_LOCALE;
    return [
        "name" => str_random(10),
        "id" => Uuid::generate()->string
    ];
});
