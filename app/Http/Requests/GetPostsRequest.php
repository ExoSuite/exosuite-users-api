<?php

namespace App\Http\Requests;

use App\Http\Requests\Abstracts\RouteParamRequest;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Dashboard;
use App\Enums\Restriction;
use App\Models\Friendship;
use App\Models\Follow;

class GetPostsRequest extends RouteParamRequest
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
            "dashboard_id" => 'required|uuid|exists:dashboards,id'
        ];
    }
}
