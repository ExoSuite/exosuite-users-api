<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 12/09/2018
 * Time: 11:33
 */

namespace App\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;


/**
 * Class FailedInternalRequestException
 */
class InternalRequestException extends Exception
{

    /**
     * Request instance
     *
     * @var $_request
     */
    private $_request;

    /**
     * Response instance
     *
     * @var $_response
     */
    private $_response;


    /**
     * Constructor
     *
     * @param Request $request The request object.
     * @param Response $response The response object.
     *
     * @return void
     */
    public function __construct(Request $request, $response)
    {
        parent::__construct();
        $this->_request = $request;
        $this->_response = $response;
    }


    /**
     * Get request object
     *
     * @return Request
     */
    public function getRequest()
    {
        return $this->_request;
    }


    /**
     * Get response object
     *
     * @return Response
     */
    public function getResponse()
    {
        return $this->_response;
    }
}
