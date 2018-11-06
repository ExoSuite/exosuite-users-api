<?php

namespace App\Http\Requests;


/**
 * Class UserSearchRequest
 * @property string $text
 * @package App\Http\Requests
 */
class UserSearchRequest extends Abstracts\RouteParamRequest
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
        return [
            'text' => 'required|string'
        ];
    }
}
