<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Middleware\AppendUserId;
use Illuminate\Support\Facades\Request;
use Illuminate\Http\Request as HttpRequest;

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
            'birthday' => 'sometimes|date_format:YYYY-MM-DD',
            'description' => 'sometimes|string|max:2048',
            'city' => 'sometimes|string|max:100'
        ];

        if (Request::isMethod(HttpRequest::METHOD_POST)) {
            $rules[AppendUserId::$key] = 'required|uuid|exists:users,id|unique:user_profiles,id';
        } else {
            $rules[AppendUserId::$key] = 'required|uuid|exists:users,id';
        }

        return $rules;
    }

    public function validated()
    {
        $data = parent::validated();
        $data[ 'id' ] = $data[ AppendUserId::$key ];
        unset($data[AppendUserId::$key]);
        return $data;
    }
}
