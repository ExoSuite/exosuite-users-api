<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 13/09/2018
 * Time: 09:53
 */

namespace App\Facades;

use Illuminate\Support\Facades\Facade;


/**
 * Class InternalRequest
 * @package App\Facades
 * @method \Illuminate\Http\Response request( string $method, string $uri, array $data = [], array $headers = [], int $statusCode = Response::HTTP_OK )
 */
class InternalRequest extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'InternalRequest';
    }
}
