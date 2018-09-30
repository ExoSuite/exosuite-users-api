<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\InternalRequestException;
use App\Facades\ApiHelper;
use App\Facades\InternalRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client;
use Laravel\Passport\PersonalAccessClient;

/**
 * Class RegisterController
 * @package App\Http\Controllers\Auth
 */
class RegisterController extends Controller
{

    /*
        |--------------------------------------------------------------------------
        | Register Controller
        |--------------------------------------------------------------------------
        |
        | This controller handles the registration of new users as well as their
        | validation and creation. By default this controller uses a trait to
        | provide this functionality without requiring any additional code.
        |
    */


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create(array_except($data, ['with_user']));
    }


    /**
     * @param RegisterUser $request see App\Http\Requests\RegisterUser
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUser $request)
    {
        $user = $this->create($request->validated());

        /** @var Response $response */
        if ($request->exists('with_user') && $request->get('with_user')) {
            return $this->created($user);
        }

        return $this->created();
    }
}
