<?php

namespace App\Http\Requests\PendingRequest;

use App\Http\Requests\Abstracts\RouteParamRequest;

/**
 * Class DeletePendingRequest
 * @package App\Http\Requests\PendingRequest
 */
class DeletePendingRequest extends RouteParamRequest
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
        ];
    }
}
