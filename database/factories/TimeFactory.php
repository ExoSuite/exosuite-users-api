<?php /** @noinspection ALL */

/** @noinspection PhpUndefinedVariableInspection */

use App\Models\Time;
use Faker\Generator as Faker;

$factory->define(Time::class, function (Faker $faker) {
    return [
        'current_time' => 1543968000, // 05 Dec 2018 00:00:00
    ];
});
