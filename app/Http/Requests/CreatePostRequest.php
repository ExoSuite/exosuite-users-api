<?php

namespace App\Http\Requests;

use App\Rules\DashboardRestrictionRule;
use Illuminate\Foundation\Http\FormRequest;

class CreatePostRequest extends FormRequest
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
            "dashboard_id" => 'required|uuid|exists:dashboards,id',
            "content" => 'required|min:1'
        ];
    }
}
