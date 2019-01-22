<?php

namespace App\Http\Requests;

use App\Http\Requests\Abstracts\RouteParamRequest;

class DeleteFriendshipRequest extends RouteParamRequest
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
            "target_id" => "required|uuid|exists:users,id"
        ];
    }
}
