<?php /** @noinspection PhpUndefinedVariableInspection */

use App\Models\Commentary;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Commentary::class, function (Faker $faker) {
    return [
        "content" => Str::random(10)
    ];
});
