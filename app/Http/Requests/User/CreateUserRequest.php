<?php declare(strict_types = 1);

namespace App\Http\Requests\User;

use App\Rules\PasswordRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class CreateUserRequest
 *
 * @package App\Http\Requests
 */
class CreateUserRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return mixed[]
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255|min:1',
            'last_name' => 'required|string|max:255|min:1',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'string', 'min:8', 'max:64', 'confirmed', new PasswordRule],
            'nick_name' => 'sometimes|max:16|min:4',
            'with_user' => 'sometimes|boolean',
        ];
    }
}
