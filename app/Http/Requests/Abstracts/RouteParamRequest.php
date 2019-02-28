<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 21/09/2018
 * Time: 13:01
 */

namespace App\Http\Requests\Abstracts;

use App\Enums\BindType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class RouteParamRequest
 *
 * @package App\Http\Requests\Abstracts
 */
abstract class RouteParamRequest extends FormRequest
{

    /**
     * Get all of the input and files for the request.
     *
     * @param  mixed[]|mixed  $keys
     * @return mixed[]
     */
    public function all($keys = null): array
    {
        // get route params
        $parameters = $this->route()->parameters();

        // swap to id instead of uuid
        if (array_key_exists(BindType::UUID, $parameters)) {
            $parameters['id'] = $parameters[BindType::UUID];
            // remove uuid key from $parameters
            unset($parameters[BindType::UUID]);
        }

        return array_replace_recursive(
            parent::all(),
            $parameters
        );
    }
}
