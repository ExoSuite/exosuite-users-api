<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\RestrictionsTypeRule;
use Illuminate\Foundation\Http\FormRequest;

class ChangeRestrictionRequest extends FormRequest
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
            "restriction" => "required|string|in:visibility,writing_restriction",
            "restriction_level" => ['required', new RestrictionsTypeRule()]
        ];
    }
}
