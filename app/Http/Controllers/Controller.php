<?php

namespace App\Http\Controllers;


use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Laravel\Passport\Client;


/**
 * Class Controller
 * @package App\Http\Controllers
 **/
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * @var Client
     */
    protected $_oauth_client;


    /**
     * Controller constructor.
     */
    public function __construct()
    {
        $this->_oauth_client = Client::whereId(2)->first();
    }
}
