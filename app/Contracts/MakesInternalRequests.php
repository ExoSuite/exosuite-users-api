<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 12/09/2018
 * Time: 11:16
 */

namespace App\Contracts;

use Illuminate\Http\Response;

/**
 * Interface MakesInternalRequests
 *
 * @package App\Contracts
 */
interface MakesInternalRequests
{

    /**
     * Make an internal request
     *
     * @param  string $method The HTTP verb to use.
     * @param  string $uri The API uri to look up.
     * @param  string[] $data The request body.
     * @param  string[] $headers Additional headers
     * @return \Illuminate\Http\Response
     */
    public function request(
        string $method,
        string $uri,
        array $data = [],
        array $headers = []
    ): Response;
}
