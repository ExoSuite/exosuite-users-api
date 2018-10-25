<?php
/**
 * Created by PhpStorm.
 * User: loiclopez
 * Date: 15/10/2018
 * Time: 18:17
 */

namespace App\Services;

use App\Enums\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Facades\ApiHelper;

/**
 * Class Horizon
 * @package App\Services
 */
class Horizon
{
    /**
     * @param Request $request
     * @return boolean|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse
     */
    public function handleAuth(Request $request)
    {
        // if user is authenticated
        if (Auth::check()) {
            return $request->user()->inRole(Roles::ADMINISTRATOR);
        }

        return ApiHelper::redirectToLogin('/horizon');
    }
}
