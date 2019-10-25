<?php declare(strict_types = 1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Rules\PasswordRule;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\View\View;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo;

    public function __construct()
    {
        $this->redirectTo = route("password.success");
    }

    public function successful(): View
    {
        return view("auth.passwords.successful");
    }

    /**
     * Get the password reset validation rules.
     *
     * @return array<string>
     */
    protected function rules(): array
    {
        return [
            'token' => 'required',
            'email' => 'required|string|email|max:255|exists:users',
            'password' => ['required', 'string', 'min:8', 'max:64', 'confirmed', new PasswordRule],
        ];
    }
}
