<?php

namespace App\Http\Requests\Like;

use App\Rules\ValidateLikeTargetRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateLikeRequest extends FormRequest
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
            'entity_id' => ['required', 'uuid', new ValidateLikeTargetRule($this->get('entity_type'))],
            'entity_type' => 'required|string|in:post,commentary,run'
        ];
    }
}
