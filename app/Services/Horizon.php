<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 15/10/2018
 * Time: 18:17
 */

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Class Horizon
 * @package App\Services
 */
class Horizon
{
    /**
     * @param Request $request
     * @return bool
     */
    public function handleAuth(Request $request): bool
    {
        dd($request->cookies, $request->user('web'), Auth::user());
        return true;
    }
}
