<?php
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
     * @var Request $request
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
     * @param Request $request The request object.
     * @param mixed $response The response object.
     *
     * @return void
     */
    public function __construct(Request $request, $response)
    {
        parent::__construct();
        $this->request = $request;
        $this->response = $response;
    }


    /**
     * Get request object
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }


    /**
     * Get response object
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }
}
