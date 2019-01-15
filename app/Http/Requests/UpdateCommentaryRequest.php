<?php

namespace App\Http\Requests;

use App\Models\Commentary;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentaryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $comm = Commentary::whereId($this->get('id'))->first();
        if ($comm['author_id'] == auth()->user()->id)
            return true;
        else
            return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'id' => 'required|uuid|exists:commentaries',
            'content' => 'required|min:1'
        ];
    }
}
