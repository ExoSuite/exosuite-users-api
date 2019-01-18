<?php

namespace App\Http\Controllers;


use App\Http\Controllers\Traits\JsonResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;


/**
 * Class Controller
 * @package App\Http\Controllers
 **/
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, JsonResponses;

    /**
     * @return string
     */
    public function alive()
    {
        return "OK";
    }
}
