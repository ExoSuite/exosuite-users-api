<?php

namespace App\Rules;

use App\Models\CheckPoint;
use Illuminate\Contracts\Validation\Rule;
use GuzzleHttp\Client;
use Phaza\LaravelPostgis\Geometries\Polygon;
use Phaza\LaravelPostgis\Geometries\Point;
use Phaza\LaravelPostgis\Geometries\LineString;

/**
 * Class PolygonRule
 * @package App\Rules
 */
class PolygonRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string $attribute
     * @param  mixed $value
     *
     * @return bool
     */
    public function passes($attribute, $value)
    {
        return (count($value) >= 5);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The parameter must be a polygon.';
    }
}