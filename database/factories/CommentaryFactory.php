<?php /** @noinspection PhpUndefinedVariableInspection */

use App\Models\Commentary;
use Faker\Generator as Faker;

$factory->define(Commentary::class, function (Faker $faker) {
    return [
        "content" => str_random(10)
    ];
});
