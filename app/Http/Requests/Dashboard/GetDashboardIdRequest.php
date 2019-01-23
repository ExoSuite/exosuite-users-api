<?php

namespace App\Http\Requests\Dashboard;

use App\Http\Requests\Abstracts\RouteParamRequest;

class GetDashboardIdRequest extends RouteParamRequest
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
            'owner_id' => 'required|uuid|exists:users,id'
        ];
    }
}
