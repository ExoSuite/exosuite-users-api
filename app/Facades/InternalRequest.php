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
 * @method \App\Facades\Response request(string $m, string $uri, array $data = [], array $headers = [], int $code = 200)
 * $m is for an HTTP method
 */
class InternalRequest extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return 'InternalRequest';
    }
}
