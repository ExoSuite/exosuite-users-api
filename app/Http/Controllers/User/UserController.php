<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\Abstracts\GetRouteParamRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Requests\User\UserSearchRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
        return $this->ok(User::with('profile')->whereId(Auth::id()));
    }

    /**
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateUserRequest $request)
    {
        Auth::user()->update($request->validated());
        return $this->noContent();
    }

    /**
     * @param UserSearchRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(UserSearchRequest $request)
    {
        $users = User::search($request->text)->with('profile')->get();
        return $this->ok($users);
    }
}
