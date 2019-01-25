<?php

use App\Enums\LikableEntities;
use App\Models\Like;
use Faker\Generator as Faker;

$factory->define(Like::class, function (Faker $faker) {
    return [
        "liked_type" => LikableEntities::POST
    ];
});
