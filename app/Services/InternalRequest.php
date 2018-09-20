<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 12/09/2018
 * Time: 11:17
 */

namespace App\Services;

use Illuminate\Http\Request;
use App\Contracts\MakesInternalRequests;
use Illuminate\Foundation\Application;
use App\Exceptions\InternalRequestException;
use Illuminate\Http\Response;

/**
 * Internal request service
 * @package App\Services
 */
class InternalRequest implements MakesInternalRequests
{

    /**
     * The app instance
     *
     * @var $_app
     */
    private $_app;


    /**
     * Constructor
     *
     * @param Application $app The app instance.
     *
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->_app = $app;
    }


    /**
     * Make an internal request
     *
     * @param  string $method The HTTP verb to use.
     * @param  string $uri The API uri to look up.
     * @param  array $data The request body.
     * @param  array $headers
     * @param int $statusCode
     * @return \Illuminate\Http\Response
     * @throws InternalRequestException if statusCode >= Response::HTTP_BAD_REQUEST
     */
    public function request(
        string $method,
        string $uri,
        array $data = [],
        array $headers = [],
        int $statusCode = Response::HTTP_OK
    )
    {
        $base_headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
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
        $response = $this->_app->handle($request);

        // Check if the request was not successful
        if ($response->getStatusCode() >= Response::HTTP_BAD_REQUEST) {
            throw new InternalRequestException($request, $response);
        }

        $response->setStatusCode($statusCode);

        // Dispatch the request
        return $response;
    }
}
