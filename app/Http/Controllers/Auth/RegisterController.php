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
        $this->middleware( 'guest' );
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create(
            [
                'first_name' => $data[ 'first_name' ],
                'last_name' => $data[ 'last_name' ],
                'email' => $data[ 'email' ],
                'password' => $data[ 'password' ],
            ]
        );
    }


    /**
     * @param RegisterUser $request see App\Http\Requests\RegisterUser
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterUser $request)
    {
        $user = $this->create( $request->validated() );

        /** @var Response $response */
        return $this->registered( $request, $user );

        // TODO: define behavior when a user is created but passport:install was not executed
    }

    /**
     * @param RegisterUser $request
     * @param User $user
     * @return mixed
     */
    protected function registered(RegisterUser $request, User $user)
    {
        $user->password = $request->get( 'password' );
        return ApiHelper::OAuth()->passwordGrant( $user, Response::HTTP_CREATED );
    }
}
