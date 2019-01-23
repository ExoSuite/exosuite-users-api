<?php

namespace App\Http\Requests\PendingRequest;

use App\Http\Requests\Abstracts\RouteParamRequest;
use App\Models\PendingRequest;

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
            'request_id' => "required|uuid|exists:pending_requests"
        ];
    }
}
