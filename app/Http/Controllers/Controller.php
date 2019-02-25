<?php declare(strict_types = 1);

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\JsonResponses;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;

/**
 * Class Controller
 *
 * @package App\Http\Controllers
 **/
class Controller extends \Illuminate\Routing\Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;
    use JsonResponses;

    public function alive(): string
    {
        return "OK";
    }
}
