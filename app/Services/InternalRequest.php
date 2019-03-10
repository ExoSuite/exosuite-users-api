<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 12/09/2018
 * Time: 11:17
 */

namespace App\Services;

use App\Contracts\MakesInternalRequests;
use App\Exceptions\InternalRequestException;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function array_merge;

/**
 * Internal request service
 *
 * @package App\Services
 */
class InternalRequest implements MakesInternalRequests
{

    /**
     * The app instance
     *
     * @var \Illuminate\Foundation\Application $app
     */
    private $app;

    /**
     * Constructor
     *
     * @param \Illuminate\Foundation\Application $app The app instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }


    /**
     * Make an internal request
     *
     * @param  string $method The HTTP verb to use.
     * @param  string $uri The API uri to look up.
     * @param  string[]|mixed[] $data The request body.
     * @param  string[]|mixed[] $headers
     * @param int $statusCode
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\InternalRequestException|\Exception if statusCode >= Response::HTTP_BAD_REQUEST
     */
    public function request(
        string $method,
        string $uri,
        array $data = [],
        array $headers = [],
        int $statusCode = Response::HTTP_OK
    ): Response
    {
        $base_headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        // Merge base_headers and headers passed from parameters
        $headers = array_merge($base_headers, $headers);

        // Create request
        $request = Request::create(
            $uri,
            $method,
            $data,
            [],
            [],
            $headers
        );

        // Get response
        /** @var \Illuminate\Http\Response $response */
        $response = $this->app->handle($request);

        // Check if the request was not successful
        if ($response->getStatusCode() >= Response::HTTP_BAD_REQUEST) {
            throw new InternalRequestException($request, $response);
        }

        $response->setStatusCode($statusCode);

        // Dispatch the request
        return $response;
    }
}
