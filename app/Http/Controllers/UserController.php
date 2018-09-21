<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserSearch;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

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

    public function search(UserSearch $request)
    {
        return User::search($request->query('text'))->get();
    }
}
