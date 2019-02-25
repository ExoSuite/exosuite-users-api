<?php declare(strict_types = 1);

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
 *
 * @package App\Facades
 * @method Response request(string $method, string $uri, array $data = [], array $headers = [], int $statusCode = Response::HTTP_OK)
 */
class InternalRequest extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'InternalRequest';
    }
}
