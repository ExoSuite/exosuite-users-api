<?php /** @noinspection PhpUndefinedVariableInspection */

/** @noinspection PhpUndefinedVariableInspection */

use Faker\Generator as Faker;

$factory->define(\App\Models\Message::class, function (Faker $faker) {
    return [
        "contents" => str_random(10)
    ];
});
