<?php declare(strict_types = 1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UserProfileRequest
 *
 * @package App\Http\Requests\User
 */
class UserProfileRequest extends FormRequest
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
            'birthday' => 'sometimes|date_format:Y-m-d',
            'description' => 'sometimes|string|max:2048',
            'city' => 'sometimes|string|max:100',
        ];
    }
}
