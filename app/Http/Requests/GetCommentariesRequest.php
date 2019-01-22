<?php

namespace App\Http\Requests;

use App\Http\Requests\Abstracts\RouteParamRequest;
use App\Models\Dashboard;
use App\Models\Follow;
use App\Models\Post;
use App\Enums\Restriction;
use App\Models\Friendship;
use Illuminate\Foundation\Http\FormRequest;

class GetCommentariesRequest extends RouteParamRequest
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
            'post_id' => 'required|uuid|exists:posts,id'
        ];
    }
}
