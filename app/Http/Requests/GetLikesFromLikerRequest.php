<?php

namespace App\Http\Requests;

use App\Models\Like;
use Illuminate\Foundation\Http\FormRequest;

class GetLikesFromLikerRequest extends FormRequest
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
            "liker_id" => "required|uuid"
        ];
    }
}
