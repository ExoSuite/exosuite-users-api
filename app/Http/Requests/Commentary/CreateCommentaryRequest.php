<?php

namespace App\Http\Requests\Commentary;

use App\Rules\CheckPostExistenceRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateCommentaryRequest extends FormRequest
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
            'content' => 'required|min:1'
        ];
    }
}
