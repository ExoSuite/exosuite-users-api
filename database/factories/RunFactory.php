<?php

use Faker\Generator as Faker;
use App\Models\Run;

$factory->define(Run::class, function (Faker $faker) {
    return [
        'name' => $faker->streetName
    ];
});
