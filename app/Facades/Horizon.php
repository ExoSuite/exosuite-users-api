<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 15/10/2018
 * Time: 18:17
 */

namespace App\Facades;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * Class Horizon
 * @package App\Services
 * @method bool handleAuth(Request $request)
 */
class Horizon extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'Horizon';
    }
}
