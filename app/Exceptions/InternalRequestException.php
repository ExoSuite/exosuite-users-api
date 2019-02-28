<?php declare(strict_types = 1);

/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 12/09/2018
 * Time: 11:33
 */

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class FailedInternalRequestException
 */
class InternalRequestException extends Exception
{

    /**
     * Request instance
     *
     * @var \Illuminate\Http\Request $request
     */
    private $request;

    /**
     * Response instance
     *
     * @var mixed $response
     */
    private $response;

    /**
     * Constructor
     *
     * @param \Illuminate\Http\Request $request The request object.
     * @param mixed $response The response object.
     */
    public function __construct(Request $request, $response)
    {
        parent::__construct();
        $this->request = $request;
        $this->response = $response;
    }


    /**
     * Get request object
     */
    public function getRequest(): Request
    {
        return $this->request;
    }


    /**
     * Get response object
     *
     * @return \Illuminate\Http\Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }
}
