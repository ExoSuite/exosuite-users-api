<?php

namespace App\Http\Requests\Like;

use App\Http\Requests\Abstracts\RouteParamRequest;

class GetLikesFromIdRequest extends RouteParamRequest
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
            'entity_id' => ['required', 'uuid', 'exists:likes,liked_id']
        ];
    }
}
