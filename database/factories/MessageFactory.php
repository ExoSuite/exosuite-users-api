<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(\App\Models\Message::class, function (Faker $faker) {
    return [
        "contents" => Str::random(),
    ];
});
