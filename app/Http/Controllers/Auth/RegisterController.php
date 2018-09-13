<?php

namespace App\Http\Controllers\Auth;

use App\Exceptions\FailedInternalRequestException;
use App\Facades\InternalRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUser;
use App\User;
use Illuminate\Http\Request;
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
        parent::__construct();
        $this->middleware('guest');
    }


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create(
            [
                'first_name' => $data[ 'first_name' ],
                'last_name' => $data[ 'last_name' ],
                'email' => $data[ 'email' ],
                'password' => Hash::make($data[ 'password' ]),
            ]
        );

    }


    /**
     * @param RegisterUser $request see App\Http\Requests\RegisterUser
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterUser $request)
    {
        //$user = $this->create($request->validated());

        return $this->registered($request, []);
    }

    /**
     * @param Request $request
     * @param $user
     * @return \Illuminate\Http\Response
     */
    protected function registered(Request $request, $user)
    {
        try {
            return InternalRequest::request(
                Request::METHOD_POST, 'oauth/token', [
                    'grant_type' => 'password',
                    'client_id' => $this->_oauth_client->id,
                    'client_secret' => $this->_oauth_client->secret,
                    'username' => $request->get('email'),
                    'password' => $request->get('password'),
                    'scope' => '',
                ]
            );
        } catch ( FailedInternalRequestException $exception ) {
            return $exception->getResponse();
        }
    }


}
