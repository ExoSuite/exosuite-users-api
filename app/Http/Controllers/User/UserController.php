<?php

namespace App\Http\Controllers\User;

use App\Http\Requests\Abstracts\GetRouteParamRequest;
use App\Http\Requests\GetTimeRequest;
use App\Http\Requests\UserSearchRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

/**
 * Class UserController
 * @package App\Http\Controllers\Personal
 */
class UserController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return $this->ok(Auth::user());
    }

    /**
     * @param UserSearchRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(UserSearchRequest $request)
    {
        $users = User::search($request->text)->get();
        return $this->ok($users);
    }
}
