<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UserProfileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'birthday' => 'sometimes|date_format:Y-m-d',
            'description' => 'sometimes|string|max:2048',
            'city' => 'sometimes|string|max:100'
        ];

        return $rules;
    }
}
