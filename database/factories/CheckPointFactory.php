<?php /** @noinspection ALL */

/** @noinspection PhpUndefinedVariableInspection */

use App\Models\CheckPoint;
use App\Http\Controllers\CheckPoint\CheckPointController;
use Faker\Generator as Faker;

$factory->define(CheckPoint::class, function (Faker $faker) {
    return [
        'type' => 'checkpoint',
        'location' => CheckPointController::createPolygonFromArray([[0.0, 0.0], [0.0, 1.0], [1.0, 1.0], [1.0, 0.0],
            [0.0, 0.0]]),
    ];
});
