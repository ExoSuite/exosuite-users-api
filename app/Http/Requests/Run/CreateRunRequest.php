<?php

namespace App\Http\Requests\Run;

use App\Rules\RunVisibilityRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateRunRequest extends FormRequest
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
            'name' => 'required|string|max:30',
            'description' => 'sometimes|string|max:255',
            'visibility' => ['sometimes', 'string', new RunVisibilityRule()]
        ];
    }
}
