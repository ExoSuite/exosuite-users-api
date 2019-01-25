<?php

use App\Models\Run;
use Faker\Generator as Faker;

$factory->define(Run::class, function (Faker $faker) {
    return [
        'name' => $faker->streetName
    ];
});
