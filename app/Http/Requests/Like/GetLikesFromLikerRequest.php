<?php

namespace App\Http\Requests\Like;

use App\Http\Requests\Abstracts\RouteParamRequest;
use App\Models\Like;
use Illuminate\Foundation\Http\FormRequest;

class GetLikesFromLikerRequest extends RouteParamRequest
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
            "user_id" => "required|uuid|exists:users,id"
        ];
    }
}
