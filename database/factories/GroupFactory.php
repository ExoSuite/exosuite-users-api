<?php

use App\Models\Group;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Group::class, function (Faker $faker) {
    return [
        "name" => Str::random(),
    ];
});
