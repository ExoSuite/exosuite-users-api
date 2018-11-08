<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 21/09/2018
 * Time: 13:01
 */

namespace App\Http\Requests\Abstracts;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RouteParamRequest
 * @package App\Http\Requests\Abstracts
 */
abstract class RouteParamRequest extends FormRequest
{
    /**
     * Get all of the input and files for the request.
     *
     * @param  array|mixed $keys
     * @return array
     */
    public function all($keys = null)
    {
        // get route params
        $parameters = $this->route()->parameters();
        // swap to id instead of uuid
        $parameters['id'] = $parameters['uuid'];
        // remove uuid key from $parameters
        unset($parameters['uuid']);

        return array_replace_recursive(
            parent::all(),
            $parameters
        );
    }
}
