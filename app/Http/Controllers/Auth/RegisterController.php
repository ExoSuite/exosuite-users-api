<?php declare(strict_types = 1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;

/**
 * Class RegisterController
 *
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
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * @param \App\Http\Requests\User\CreateUserRequest $request see \App\Http\Requests\CreateUserRequest
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(CreateUserRequest $request): JsonResponse
    {
        $user = $this->create($request->validated());

        Dashboard::create([
            'owner_id' => $user->id,
        ]);

        /** @var \Illuminate\Http\Response $response */
        if ($request->exists('with_user') && $request->get('with_user')) {
            return $this->created($user);
        }

        return $this->created();
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  string[] $data
     * @return \App\Models\User
     */
    protected function create(array $data): User
    {
        return User::create(Arr::except($data, ['with_user']));
    }
}
