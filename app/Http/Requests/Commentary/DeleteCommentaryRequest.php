<?php

namespace App\Http\Requests\Commentary;

use App\Http\Requests\Abstracts\RouteParamRequest;
use App\Models\Commentary;
use App\Models\Dashboard;
use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class DeleteCommentaryRequest extends RouteParamRequest
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
            'commentary_id' => 'required|uuid|exists:commentaries,id'
        ];
    }
}
